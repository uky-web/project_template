#####
# Local_packages.sh
# Code in git repos of your composer project's dependencies 
# 
# 1. Add composer dependencies you want to code in to ./local_packages.yaml
# 2. Run this script.
# 3. Run composer install.
# 
# Any packages you specify will be added to your root composer.json as dev dependencies
# and symlinked from a /packages directory

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

source "$SCRIPT_DIR/parse_yaml.sh"
eval $(parse_yaml $SCRIPT_DIR/local_packages.yaml)

# Remove the local packages if they are already in composer 
for f in $packages_ ; 
    do 
        package_name=${f}_package
        package_git=${f}_git
        package_version=${f}_version
        package_branch=${f}_branch
        package_composer_name=${f}_composername

        ddev composer remove  --no-update ${!package_name}

done

# Clone the packages from their git repos and add them as path composer repostitories
for f in $packages_ ; 
    do 
        package_name=${f}_package
        package_git=${f}_git
        package_version=${f}_version
        package_branch=${f}_branch
        package_composer_name=${f}_composername

        #Set up packages directory
        mkdir $SCRIPT_DIR/../../packages -p

        #Clone the package and checkout the specified branch. Fetch it if it's already created.
        if [ -d $SCRIPT_DIR/../../packages/${!package_name} ]
        then
            git -C $SCRIPT_DIR/../../packages/${!package_name} fetch origin ${!package_branch}
        else 
            git clone ${!package_git} $SCRIPT_DIR/../../packages/${!package_name}
        fi
        git -C $SCRIPT_DIR/../../packages/${!package_name} checkout ${!package_branch}

        #Add the created package directory as a composer path repository
        ddev composer config repositories.${!package_name} path /var/www/html/packages/${!package_name}

done

# Require the locally specfied version of the packages
for f in $packages_ ; 
    do 
        package_name=${f}_package
        package_git=${f}_git
        package_version=${f}_version
        package_branch=${f}_branch
        package_composer_name=${f}_composername

        #Add the package as a dependency
        ddev composer require --no-update -vvv --dev "${!package_name}:${!package_composer_name} as ${!package_version}"       

done



