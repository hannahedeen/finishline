import sys

import re

DEBUG = False
INTENT_WEIGHTS = {
                    #portable   budget  workstation gaming  fast    storage touchscreen screenquality
    "photography": [7.0,        3.0,    6.0,        2.0,    6.0,    10.0,   1.0,        10.0],
    "gaming":      [2.0,        2.0,    7.0,        8.0,    8.0,    7.0,    0.0,        7.0],
    "budget":      [6.0,        20.0,   2.0,        1.0,    3.0,    5.0,    3.0,        1.0],
    "student":     [9.0,        5.0,    3.0,        4.0,    8.0,    2.0,    0.0,        4.0],
    "business":    [10.0,       3.0,    10.0,       0.0,    8.0,    5.0,    0.0,        8.0]
}


def read_in_csv(filename):
    database = []
    with open(filename) as a:
        for line in a:
            database.append(line.split(","))

    return database


def extract_max_price(intents):
    intents_with_numbers = []
    max_price = 100000000.0
    for intent in intents.split(","):
        # This is a number
        if re.match(r"[0-9]+", intent):
            intents_with_numbers.append(intent)
            max_price = float(intent)

    for intent_with_number in intents_with_numbers:
        intents = intents.replace(intent_with_number, "")

    intents = intents.replace(",,", ",")

    return intents, max_price


def find_relevant_columns_in_database(database):
    relevants = []
    ratings = []
    for row in database:
        relevants.append([row[0], float(row[12])])
        ratings.append(map(float, row[15:]))

    return relevants, ratings

def id_max(values):
    id = -1
    max_value = 0.00
    for i, value in enumerate(values):
        if value > max_value:
            max_value = value
            id = i

    return id

def filter_by_price(relevants, ratings, max_price):
    new_relevants = []
    new_ratings = []
    for relevant, rating in zip(relevants, ratings):
        if relevant[1] <= max_price:
            new_relevants.append(relevant)
            new_ratings.append(rating)

    return new_relevants, new_ratings


def find_product(intents, database, max_price):
    relevants, ratings = find_relevant_columns_in_database(database)
    relevants, ratings = filter_by_price(relevants, ratings, max_price)

    # There are no laptops that fit the price range
    if len(relevants) == 0 and len(ratings) == 0:
        return ""

    scores = [0] * len(database)
    for intent in intents:
        if intent in INTENT_WEIGHTS:
            weights = INTENT_WEIGHTS[intent]
            for laptop_id, rating in enumerate(ratings):
                scores[laptop_id] += sum([x * y for x, y in zip(rating, weights)])

    if DEBUG:
        for x in [(relevant[0], score) for relevant, score in zip(relevants, scores)]:
            print x

    return relevants[id_max(scores)][0]


if __name__ == "__main__":
    str_intents = sys.argv[1] if len(sys.argv) > 1 else ""

    # Extract max_price
    str_intents, max_price = extract_max_price(str_intents)

    # Remove comma at the end (if it is there)
    str_intents = str_intents[:-1] if str_intents.endswith(",") else str_intents

    # Read in data from the database
    database = read_in_csv("D:\\xampp\\htdocs\\chatBotShopAss\\data.csv")

    # Output the correct value
    print find_product(str_intents.split(","), database[1:], max_price)
