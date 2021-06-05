from json import dumps
from httplib2 import Http
import mysql.connector
from yahoo_fin import stock_info as si
from _ast import stmt

mydb = mysql.connector.connect(
  host="127.0.0.1",
  user="stock",
  passwd="Pass@1234",
  database="stocks"
)


def postToChat(card):
    """Hangouts Chat incoming webhook quickstart."""
    url = 'https://chat.googleapis.com/v1/spaces/AAAAttO5lGo/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=O8jRrOKFDlrVSMwE5IFcqiq5gSThdFhh4nG4OYbUjqk%3D'
    bot_message = {
                      "cards": card
                }

    message_headers = {'Content-Type': 'application/json; charset=UTF-8'}

    http_obj = Http()
#     print bot_message
    response = http_obj.request(
        uri=url,
        method='POST',
        headers=message_headers,
        body=dumps(bot_message),
    )

#     print(response)

def FetchFromDBStatemnt(stmt):
    mycursor = mydb.cursor()
    try:
        mycursor.execute(stmt)
        myresult = mycursor.fetchall()
        return myresult
    except mysql.connector.Error as err:
        print err
        
def ExecuteDBStatemnt(stmt):
    mycursor = mydb.cursor()
    try:
        mycursor.execute(stmt)
        mydb.commit()
    except mysql.connector.Error as err:
        print err

def get_current_price(cmpy_code):
    curr_price = si.get_live_price(cmpy_code)
    open_price = si.get_quote_table(cmpy_code)["Open"]
    year_range = si.get_quote_table(cmpy_code)['52 Week Range']
    year_low = year_range.split('-')[0].strip()
    year_high = year_range.split('-')[1].strip()
    day_range = si.get_quote_table(cmpy_code)["Day's Range"]
    day_low = day_range.split('-')[0].strip()
    day_high = day_range.split('-')[1].strip()
    return curr_price, open_price, year_low, year_high, day_low, day_high
    
def update_cmpy_curr_price(cmpy_code, curr_price):
    stmt = "update company_details set curr_price = %s where company_code = '%s' ;" % (curr_price, cmpy_code)
    ExecuteDBStatemnt(stmt)
    
def create_cmpy_payload(cmpy_name, cmpy_code, cmpy_logo, curr_price, open_price, link):
    cmpy_msg = {
                  "header": {
                    "title": cmpy_name,
                    "subtitle": cmpy_code,
                    "imageUrl": cmpy_logo
                  },
                  "sections": [
                    {
                      "widgets": [
                        {
                            "keyValue": {
                              "topLabel": "Current Price.",
                              "content": str(curr_price)
                            }
                        },
                        {
                            "keyValue": {
                              "topLabel": "Opening Price.",
                              "content": str(open_price)
                            }
                        }
                      ] 
                    },
                    {
                        "widgets": [
                                  {
                                  "buttons": [
                                    {
                                      "textButton": {
                                        "text": "Yahoo Link",
                                        "onClick": {
                                          "openLink": {
                                            "url": link
                                          }
                                        }
                                      }
                                    }
                                  ]
                              }
                        ]
                    }
                ]
            }
    return cmpy_msg

def main():
    stmt = 'select * from company_details;'
    result = FetchFromDBStatemnt(stmt)
    card = []
    for res in result:
        cmpy_name = res[1]
        cmpy_code = res[2]
        cmpy_logo = res[4]
        curr_price, open_price, year_low, year_high, day_low, day_high = get_current_price(cmpy_code)
        update_cmpy_curr_price(cmpy_code, curr_price)
        link = 'https://in.finance.yahoo.com/quote/%s?p=%s' % (cmpy_code, cmpy_code)
        cmpy_msg = create_cmpy_payload(cmpy_name, cmpy_code, cmpy_logo, round(curr_price, 2), open_price, link)
        card.append(cmpy_msg)
    postToChat(card)
if __name__ == '__main__':
    main()
