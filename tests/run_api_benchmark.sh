# First same endpoints (fast in general)
seq 1 500 | xargs -n1 -P3  curl "http://167.86.75.179:8001/api/crypto/historical/value/1590068880/1590069960/kraken/1m/btc/usd" &
seq 1 500 | xargs -n1 -P1 curl "http://167.86.75.179:8001/api/crypto/historical/value/1589760000/1590069960/bitfinex/1h/eth/eur" &
seq 1 500 | xargs -n1 -P2  curl "http://167.86.75.179:8001/api/crypto/historical/value/1588636800/1590069960/gdax/1d/xrp/czk" &
seq 1 500 | xargs -n1 -P4  curl "http://167.86.75.179:8001/api/crypto/historical/value/1588636800/1590069960/binance/12h/xrp/czk" &

# Seconds different endpoints (mix of fast and slower)
#seq 1 100 | xargs -n1 -P3  curl "http://167.86.75.179:8001/api/crypto/historical/asset/value/eth/usd/1588788660/1588875060" &
#seq 1 500 | xargs -n1 -P1 curl "http://167.86.75.179:8001/api/crypto/historical/value/1589760000/1590069960/bitfinex/1h/eth/eur" &
#seq 1 200 | xargs -n1 -P2  curl "http://167.86.75.179:8001/api/crypto/historical/asset/value/btc/usd/1588788660/1588875060" &
#seq 1 500 | xargs -n1 -P4  curl "http://167.86.75.179:8001/api/crypto/historical/value/1588636800/1590069960/binance/12h/xrp/czk" &
#seq 1 400 | xargs -n1 -P4  curl "http://167.86.75.179:8001/api/crypto/historical/volume/1588636800/1588875060/kraken/1h/btc/jpy"
