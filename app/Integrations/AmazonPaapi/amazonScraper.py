from amazon_paapi import AmazonApi
import sys
import requests
import re
import json

ACCESS_KEY = 'AKIAJAPK6GYSV2EKH57Q'
SECRET_KEY = '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'
ASSOCIATE_TAG = 'sdrumoid-21'
COUNTRY = 'IT'


def generate_referral_link(asin):
    referral_link = f'https://www.amazon.it/dp/{asin}/?tag={ASSOCIATE_TAG}'
    return referral_link


def extract_asin_from_amazon_short_url(short_url):
    response = requests.get(short_url)
    final_url = response.url
    asin_pattern = r'/([A-Z0-9]{10})(?:[/?]|$)'
    match = re.search(asin_pattern, final_url)

    if match:
        return match.group(1)
    return None

try:
    amazon = AmazonApi(ACCESS_KEY, SECRET_KEY, ASSOCIATE_TAG, COUNTRY)
    url = extract_asin_from_amazon_short_url(sys.argv[1])
    product = amazon.get_items(url)[0]


    # Store offers once for readability
    offers = product.offers

    productInfo = {
        "title": product.item_info.title.display_value,
        "actualPrice": offers.listings[0].price.amount if offers is not None else None,
        "lowerPrice":  offers.summaries[0].lowest_price.amount if offers is not None else None,
        "highestPrice":  offers.summaries[0].highest_price.amount if offers is not None else None,
        "url": generate_referral_link(product.asin),
        "photo": product.images.primary.large.url
    }
    amazon = None
    print(json.dumps(productInfo))
    exit()
except:
    pass