exports.PullquoteCompponent = 
class PullquoteComponent {
    constructor(page){
        this.page = page;
        this.usernameInput = '#edit-name';
        this.passwordInput = '#edit-pass';
        this.loginButton = '#edit-submit';
        this.pageURl = process.env.URL;
    }
    async gotoLoginPage(){
        await this.page.goto(String(this.pageURl));
    }
    async Login(){
        await this.page.locator(this.usernameInput).fill(String(process.env.USERNAME));
        await this.page.locator(this.passwordInput).fill(String(process.env.USERPASS));
        await this.page.locator(this.loginButton).click();
    }
}