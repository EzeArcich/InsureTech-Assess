{!! html_entity_decode(strip_tags(str_replace(['</p>', '</div>', '<br>', '<br/>', '<br />', '</li>'], "\n", $messageBody)), ENT_QUOTES | ENT_HTML5, 'UTF-8') !!}
