STEPS:

- removed 301 redirect as it's caused event validation to fail without actual HTTP 200 POST payload
- tested and checked variable/array value if they are returning the expected value
- ran through each error from heroku logs from every change made
- added some temp 'echo' on some section just to check if the code has reach running certain parts, as a way to isolate where the code is failing

- created API call for the user's info as user_change payload does not have the display/real name needed for the message to be posted on the channel
- created a CURL call for the API endpoint
- worked around errors

- on postMessage Section, change call URL to a URL I was more confident in handling
- change some CURL parameters, specially the headers