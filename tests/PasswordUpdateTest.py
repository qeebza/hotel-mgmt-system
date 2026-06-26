from selenium import webdriver

def test_homepage_load():

    driver = webdriver.Chrome()

    driver.get("http://localhost/hotel-mgmt-system-develop/")

    assert driver.title != ""

    driver.quit()