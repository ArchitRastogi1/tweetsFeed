<?php

namespace Validators;

class GetTweetsValidator {
    public function validateTweetGetData($hashTag) {
        if(empty($hashTag)) {
            return "No #Tag is mentioned,";
        }
        return NULL;
    }
}

