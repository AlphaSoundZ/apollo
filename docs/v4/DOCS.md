# REST API-Documentation

This page contains the documentation for the REST API.

## Endpoints

### User

* [POST /api/v4/user/create](#create-user)

* [PATCH /api/v4/user/change](#modify-user) - not available yet

* [DELETE /api/v4/user/delete](#delete-user) - not available yet

* [GET /api/v4/user](#get-user)

* [GET /api/v4/user/{id}](#get-user-by-id)

* [GET /api/v4/user/{class_name}](#get-user-by-class)

* [GET /api/v4/user?query={query}](#get-user-by-query)

* [GET /api/v4/user/{id}/history](#get-user-history)

* [GET /api/v4/user?booking=true](#get-user-by-booking)

---

### Create User

#### Description

Creates a new user.

#### Request

##### HTTP-Method

`POST`

##### URL

`/api/v4/user/create`

##### Parameter

| Parameter | type | Required | Description | Related Endpoint |
| --- | --- | --- | --- | --- |
| `firstname` | string | yes | The first name of the user. | - |
| `lastname` | string | yes | The last name of the user. | - |
| `class_id` | integer | yes | The id of the class of the user. | `/user/class` |
| `usercard_id` | integer | no | The id of the usercard of the user. A user does not necessarily have to have a user card. The usercard can be added later using `/user/change`. | `/usercard` |
| `token_id` | integer | no | The id of the token of the user. A user does not necessarily have to have a token. | `/token` |
| `ignore_duplicates` | boolean | no | If set to `true`, the user will be created even if a user with the same first and last name already exists. | - |

##### Response Codes

| Code | Description |
| --- | --- |
| `SUCCESS` | The user was created successfully. |
| `USER_ALREADY_EXISTS` | A user with the same first and last name already exists. |
| `CLASS_NOT_FOUND` | The class with the given id does not exist. |
| `USERCARD_NOT_FOUND` | The usercard with the given id does not exist. |
| `TOKEN_NOT_FOUND` | The token with the given id does not exist. |
| `USERCARD_ALREADY_EXISTS` | The usercard with the given id is already assigned to another user. |
| `USERCARD_TYPE_NOT_FOUND` | The usercard type of the usercard with the given id does not exist. |

##### Response Body

| Parameter | type | Description |
| --- | --- | --- |
| `user_id` | string | The id of the created user. |

##### Example

```json
{
    "firstname": "Max",
    "lastname": "Mustermann",
    "class_id": 1,
    "usercard_id": 1,
    "token_id": 1,
    "ignore_duplicates": false
}
```

---
