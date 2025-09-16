<?php

// Redirection helper
function redirect($page) {
    header("location: " . URLROOT . "/" . $page);
}