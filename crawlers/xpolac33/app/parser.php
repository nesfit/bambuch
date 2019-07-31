<?php

class Parser {

    function parsing($url) {
        $doc = new DOCDocument();
        $doc->loadHTML( file_get_html($url));
    }

}
