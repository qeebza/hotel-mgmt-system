import time
import pytest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager

BASE_URL = "http://localhost/hotel-mgmt-system"


@pytest.fixture
def driver():
    driver = webdriver.Chrome(
        service=Service(
            ChromeDriverManager().install()
        )
    )

    driver.maximize_window()
    yield driver
    driver.quit()


def open_login(driver):
    driver.get(f"{BASE_URL}/sign-in.php")


# =====================================
# TC-AT-01
# Email Field Validation
# =====================================

def test_email_field_validation(driver):

    open_login(driver)

    email = driver.find_element(
        By.ID,
        "loginEmail"
    )

    assert email.get_attribute("type") == "email"


# =====================================
# TC-AT-02
# Password Required Validation
# =====================================

def test_password_required(driver):

    open_login(driver)

    driver.find_element(
        By.ID,
        "loginEmail"
    ).send_keys("test@gmail.com")

    driver.find_element(
        By.NAME,
        "loginSubmitBtn"
    ).click()

    password = driver.find_element(
        By.ID,
        "loginPassword"
    )

    validation_message = driver.execute_script(
        "return arguments[0].validationMessage;",
        password
    )

    assert validation_message != ""


# =====================================
# TC-AT-03
# Invalid Login Credentials
# =====================================

def test_invalid_login(driver):

    open_login(driver)

    driver.find_element(
        By.ID,
        "loginEmail"
    ).send_keys("test@gmail.com")

    driver.find_element(
        By.ID,
        "loginPassword"
    ).send_keys("wrongpassword")

    driver.find_element(
        By.NAME,
        "loginSubmitBtn"
    ).click()

    time.sleep(2)

    page = driver.page_source.lower()

    assert (
        "invalid email or password" in page
        or
        "incorrect password" in page
    )


# =====================================
# TC-AT-04
# Login Attempt Lockout
# =====================================

def test_login_lockout(driver):

    open_login(driver)

    for i in range(5):

        driver.find_element(
            By.ID,
            "loginEmail"
        ).clear()

        driver.find_element(
            By.ID,
            "loginPassword"
        ).clear()

        driver.find_element(
            By.ID,
            "loginEmail"
        ).send_keys("admin@gmail.com")

        driver.find_element(
            By.ID,
            "loginPassword"
        ).send_keys("wrongpassword")

        driver.find_element(
            By.NAME,
            "loginSubmitBtn"
        ).click()

        time.sleep(1)

    driver.refresh()

    driver.find_element(
        By.ID,
        "loginEmail"
    ).send_keys("admin@gmail.com")

    driver.find_element(
        By.ID,
        "loginPassword"
    ).send_keys("wrongpassword")

    driver.find_element(
        By.NAME,
        "loginSubmitBtn"
    ).click()

    time.sleep(2)

    page = driver.page_source.lower()

    assert (
        "too many failed login attempts" in page
        or
        "try again" in page
    )


# =====================================
# TC-AT-05
# Admin Access Protection
# =====================================

def test_admin_requires_login(driver):

    driver.get(
        f"{BASE_URL}/admin.php"
    )

    time.sleep(2)

    assert (
        "sign-in" in driver.current_url.lower()
        or
        "login" in driver.page_source.lower()
    )


# =====================================
# TC-AT-06
# Session Regeneration
# =====================================

def test_session_regeneration(driver):

    open_login(driver)

    before_cookie = driver.get_cookie(
        "PHPSESSID"
    )

    # Replace with valid account
    driver.find_element(
        By.ID,
        "loginEmail"
    ).send_keys("admin@gmail.com")

    driver.find_element(
        By.ID,
        "loginPassword"
    ).send_keys("admin123")

    driver.find_element(
        By.NAME,
        "loginSubmitBtn"
    ).click()

    time.sleep(3)

    after_cookie = driver.get_cookie(
        "PHPSESSID"
    )

    if before_cookie and after_cookie:
        assert (
            before_cookie["value"]
            !=
            after_cookie["value"]
        )