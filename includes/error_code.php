<?php

enum ErrorCode: int {

    case UNKNOWN = -999;
    case API_KEY = 1;
    case AUTHENTICATION = 2;
    case MISSING_PARAMETER = 3;
    case USER_EXISTS = 4;
    case GENERIC_FAILED = 5;
    case INVALID_USER_IDENTIFIER = 6;
    case WRONG_PASSWORD = 7;
    case DELETE_FAILED = 8;
    case CANNOT_BE_DELETED = 9;
    case AUTHORIZATION_FAILED = 10;
    case UPDATE_FAILED = 11;
    case CREATE_FAILED = 12;
}