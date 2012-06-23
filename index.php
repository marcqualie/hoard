<?php

/**
 * Root Redirect
 *
 * This file is only here incase Hoard is installed directly inside a sub folder
 */

header('Location: ' . str_replace('//', '/', $_SERVER['REQUEST_URI'] . '/server'));