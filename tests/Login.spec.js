// @ts-check
const { test, expect } = require("@playwright/test");
const { LoginPage } = require("./pages/LoginPage");

//test-commit
test("Test Site Login @smoke", async ({ page }) => {
  const login = new LoginPage(page);
  await login.gotoLoginPage();
  await login.Login();
  await expect(page).toHaveTitle("admin | uky_base");
});
