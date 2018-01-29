import sys

if __name__ == "__main__":
    x = sys.argv[1] if len(sys.argv) > 1 else ""

    with open("D:\\xampp\\htdocs\\chatBotShopAss\\cache.txt", "w") as a:
        a.write(x)
