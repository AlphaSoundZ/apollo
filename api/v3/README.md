Features added:
- Added a new Routing system for the API
- Following GET endpoints are now available:
    + /api/v3/user/:id
    + /api/v3/user/:id
    + /api/v3/user?query=...
    + /api/v3/user/:id/history
    + /api/v3/user?booking=true
    + /api/v3/device
    + /api/v3/device/:id
    + /api/v3/device/:uid
    Options:
    + ?query=... (searches for users with the given query)
    + ?booking=true (returns all users with a booking)
    + page and size - for pagination
    + strict - if true, the search will be limited to 100% accordance, but its not case sensitive