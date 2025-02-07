// @ts-check
const { test, expect } = require("@playwright/test");
const { LoginPage } = require("./pages/LoginPage");
const { PullquoteComponent } = require("./pages/PullquoteComponent"); 


test("Pullquote Test", async ({ page }) => {
  const login = new LoginPage(page);
  await login.gotoLoginPage();
  await login.Login();
  await expect(page).toHaveTitle("admin | uky_base");
});
