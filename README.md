# Usage
    docker-compose up -d
    docker-compose exec php sh
    composer install
    bin/console sse:server

# Notes
Browsers has own limitations on streaming connections. For example, Chrome has 6 connections per domain. So, if you open 6 tabs with this app, you will not see any updates. You can use different domains or incognito mode to avoid this limitation.

https://github.com/orgs/reactphp/discussions/507#discussioncomment-5909988
https://stackoverflow.com/questions/18584525/server-sent-events-and-browser-limits
