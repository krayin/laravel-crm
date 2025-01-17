<?php

namespace Webkul\Email\Helpers;

class HtmlFilter
{
    /**
     * Tag print.
     *
     * @param  string  $tagname
     * @param  array  $attary
     * @param  int  $tagtype
     * @return string
     */
    public function tln_tagprint($tagname, $attary, $tagtype)
    {
        if ($tagtype == 2) {
            $fulltag = '</'.$tagname.'>';
        } else {
            $fulltag = '<'.$tagname;

            if (is_array($attary) && count($attary)) {
                $atts = [];

                foreach ($attary as $attname => $attvalue) {
                    array_push($atts, "$attname=$attvalue");
                }

                $fulltag .= ' '.implode(' ', $atts);
            }

            if ($tagtype == 3) {
                $fulltag .= ' /';
            }

            $fulltag .= '>';
        }

        return $fulltag;
    }

    /**
     * A small helper function to use with array_walk. Modifies a by-ref
     * value and makes it lowercase.
     *
     * @param  string  $val
     * @return void
     */
    public function tln_casenormalize(&$val)
    {
        $val = strtolower($val);
    }

    /**
     * This function skips any whitespace from the current position within
     * a string and to the next non-whitespace value.
     *
     * @param  string  $body
     * @param  int  $offset
     * @return int
     */
    public function tln_skipspace($body, $offset)
    {
        preg_match('/^(\s*)/s', substr($body, $offset), $matches);

        try {
            if (! empty($matches[1])) {
                $count = strlen($matches[1]);
                $offset += $count;
            }
        } catch (\Exception $e) {
        }

        return $offset;
    }

    /**
     * This function looks for the next character within a string.  It's
     * really just a glorified "strpos", except it catches the failures
     * nicely.
     *
     * @param  string  $body
     * @param  int  $offset
     * @param  string  $needle
     * @return int
     */
    public function tln_findnxstr($body, $offset, $needle)
    {
        $pos = strpos($body, $needle, $offset);

        if ($pos === false) {
            $pos = strlen($body);
        }

        return $pos;
    }

    /**
     * This function takes a PCRE-style regexp and tries to match it
     * within the string.
     *
     * @param  string  $body
     * @param  int  $offset
     * @param  string  $reg
     * @return array|bool
     */
    public function tln_findnxreg($body, $offset, $reg)
    {
        $matches = $retarr = [];

        $preg_rule = '%^(.*?)('.$reg.')%s';

        preg_match($preg_rule, substr($body, $offset), $matches);

        if (! isset($matches[0]) || ! $matches[0]) {
            $retarr = false;
        } else {
            $retarr[0] = $offset + strlen($matches[1]);

            $retarr[1] = $matches[1];

            $retarr[2] = $matches[2];
        }

        return $retarr;
    }

    /**
     * This function looks for the next tag.
     *
     * @param  string  $body
     * @param  int  $offset
     * @return array|bool
     */
    public function tln_getnxtag($body, $offset)
    {
        if ($offset > strlen($body)) {
            return false;
        }

        $lt = $this->tln_findnxstr($body, $offset, '<');

        if ($lt == strlen($body)) {
            return false;
        }

        /**
         * We are here:
         * blah blah <tag attribute="value">
         * \---------^
         */
        $pos = $this->tln_skipspace($body, $lt + 1);

        if ($pos >= strlen($body)) {
            return [false, false, false, $lt, strlen($body)];
        }

        /**
         * There are 3 kinds of tags:
         * 1. Opening tag, e.g.:
         *    <a href="blah">
         * 2. Closing tag, e.g.:
         *    </a>
         * 3. XHTML-style content-less tag, e.g.:
         *    <img src="blah"/>
         */
        switch (substr($body, $pos, 1)) {
            case '/':
                $tagtype = 2;

                $pos++;
                break;

            case '!':
                /**
                 * A comment or an SGML declaration.
                 */
                if (substr($body, $pos + 1, 2) == '--') {
                    $gt = strpos($body, '-->', $pos);

                    if ($gt === false) {
                        $gt = strlen($body);
                    } else {
                        $gt += 2;
                    }

                    return [false, false, false, $lt, $gt];
                } else {
                    $gt = $this->tln_findnxstr($body, $pos, '>');

                    return [false, false, false, $lt, $gt];
                }
                break;

            default:
                $tagtype = 1;
                break;

        }

        /**
         * Look for next [\W-_], which will indicate the end of the tag name.
         */
        $regary = $this->tln_findnxreg($body, $pos, '[^\w\-_]');

        if ($regary == false) {
            return [false, false, false, $lt, strlen($body)];
        }

        [$pos, $tagname, $match] = $regary;

        $tagname = strtolower($tagname);

        /**
         * $match can be either of these:
         * '>'  indicating the end of the tag entirely.
         * '\s' indicating the end of the tag name.
         * '/'  indicating that this is type-3 xhtml tag.
         *
         * Whatever else we find there indicates an invalid tag.
         */
        switch ($match) {
            case '/':
                /**
                 * This is an xhtml-style tag with a closing / at the
                 * end, like so: <img src="blah"/>. Check if it's followed
                 * by the closing bracket. If not, then this tag is invalid
                 */
                if (substr($body, $pos, 2) == '/>') {
                    $pos++;

                    $tagtype = 3;
                } else {
                    $gt = $this->tln_findnxstr($body, $pos, '>');

                    $retary = [false, false, false, $lt, $gt];

                    return $retary;
                }

                // intentional fall-through
            case '>':
                return [$tagname, false, $tagtype, $lt, $pos];

            default:
                /**
                 * Check if it's whitespace.
                 */
                if (! preg_match('/\s/', $match)) {
                    /**
                     * This is an invalid tag! Look for the next closing ">".
                     */
                    $gt = $this->tln_findnxstr($body, $lt, '>');

                    return [false, false, false, $lt, $gt];
                }

                break;
        }

        /**
         * At this point we're here:
         * <tagname  attribute='blah'>
         * \-------^
         *
         * At this point we loop in order to find all attributes.
         */
        $attary = [];

        while ($pos <= strlen($body)) {
            $pos = $this->tln_skipspace($body, $pos);

            if ($pos == strlen($body)) {
                /**
                 * Non-closed tag.
                 */
                return [false, false, false, $lt, $pos];
            }

            /**
             * See if we arrived at a ">" or "/>", which means that we reached
             * the end of the tag.
             */
            $matches = [];

            if (preg_match('%^(\s*)(>|/>)%s', substr($body, $pos), $matches)) {
                /**
                 * Yep. So we did.
                 */
                $pos += strlen($matches[1]);

                if ($matches[2] == '/>') {
                    $tagtype = 3;

                    $pos++;
                }

                return [$tagname, $attary, $tagtype, $lt, $pos];
            }

            /**
             * There are several types of attributes, with optional
             * [:space:] between members.
             * Type 1:
             *   attrname[:space:]=[:space:]'CDATA'
             * Type 2:
             *   attrname[:space:]=[:space:]"CDATA"
             * Type 3:
             *   attr[:space:]=[:space:]CDATA
             * Type 4:
             *   attrname
             *
             * We leave types 1 and 2 the same, type 3 we check for
             * '"' and convert to "&quot" if needed, then wrap in
             * double quotes. Type 4 we convert into:
             * attrname="yes".
             */
            $regary = $this->tln_findnxreg($body, $pos, '[^\w\-_]');

            if ($regary == false) {
                /**
                 * Looks like body ended before the end of tag.
                 */
                return [false, false, false, $lt, strlen($body)];
            }

            [$pos, $attname, $match] = $regary;

            $attname = strtolower($attname);

            /**
             * We arrived at the end of attribute name. Several things possible
             * here:
             * '>'  means the end of the tag and this is attribute type 4
             * '/'  if followed by '>' means the same thing as above
             * '\s' means a lot of things -- look what it's followed by.
             *      anything else means the attribute is invalid.
             */
            switch ($match) {
                case '/':
                    /**
                     * This is an xhtml-style tag with a closing / at the
                     * end, like so: <img src="blah"/>. Check if it's followed
                     * by the closing bracket. If not, then this tag is invalid
                     */
                    if (substr($body, $pos, 2) == '/>') {
                        $pos++;
                        $tagtype = 3;
                    } else {
                        $gt = $this->tln_findnxstr($body, $pos, '>');
                        $retary = [false, false, false, $lt, $gt];

                        return $retary;
                    }

                    // intentional fall-through
                case '>':
                    $attary[$attname] = '"yes"';

                    return [$tagname, $attary, $tagtype, $lt, $pos];
                    break;

                default:
                    /**
                     * Skip whitespace and see what we arrive at.
                     */
                    $pos = $this->tln_skipspace($body, $pos);

                    $char = substr($body, $pos, 1);
                    /**
                     * Two things are valid here:
                     * '=' means this is attribute type 1 2 or 3.
                     * \w means this was attribute type 4.
                     * anything else we ignore and re-loop. End of tag and
                     * invalid stuff will be caught by our checks at the beginning
                     * of the loop.
                     */
                    if ($char == '=') {
                        $pos++;

                        $pos = $this->tln_skipspace($body, $pos);
                        /**
                         * Here are 3 possibilities:
                         * "'"  attribute type 1
                         * '"'  attribute type 2
                         * everything else is the content of tag type 3
                         */
                        $quot = substr($body, $pos, 1);

                        if ($quot == '\'') {
                            $regary = $this->tln_findnxreg($body, $pos + 1, '\'');

                            if ($regary == false) {
                                return [false, false, false, $lt, strlen($body)];
                            }

                            [$pos, $attval, $match] = $regary;

                            $pos++;

                            $attary[$attname] = '\''.$attval.'\'';
                        } elseif ($quot == '"') {
                            $regary = $this->tln_findnxreg($body, $pos + 1, '\"');

                            if ($regary == false) {
                                return [false, false, false, $lt, strlen($body)];
                            }

                            [$pos, $attval, $match] = $regary;

                            $pos++;

                            $attary[$attname] = '"'.$attval.'"';
                        } else {
                            /**
                             * These are hateful. Look for \s, or >.
                             */
                            $regary = $this->tln_findnxreg($body, $pos, '[\s>]');

                            if ($regary == false) {
                                return [false, false, false, $lt, strlen($body)];
                            }

                            [$pos, $attval, $match] = $regary;

                            $attval = preg_replace('/\"/s', '&quot;', $attval);

                            $attary[$attname] = '"'.$attval.'"';
                        }
                    } elseif (preg_match('|[\w/>]|', $char)) {
                        $attary[$attname] = '"yes"';
                    } else {
                        $gt = $this->tln_findnxstr($body, $pos, '>');

                        return [false, false, false, $lt, $gt];
                    }
                    break;
            }
        }

        /**
         * The fact that we got here indicates that the tag end was never
         * found. Return invalid tag indication so it gets stripped.
         */
        return [false, false, false, $lt, strlen($body)];
    }

    /**
     * Translates entities into literal values so they can be checked.
     *
     * @param  string  $attvalue
     * @param  string  $regex
     * @param  bool  $hex
     * @return bool
     */
    public function tln_deent(&$attvalue, $regex, $hex = false)
    {
        preg_match_all($regex, $attvalue, $matches);

        if (is_array($matches) && count($matches[0]) > 0) {
            $repl = [];

            for ($i = 0; $i < count($matches[0]); $i++) {
                $numval = $matches[1][$i];

                if ($hex) {
                    $numval = hexdec($numval);
                }

                $repl[$matches[0][$i]] = chr($numval);
            }

            $attvalue = strtr($attvalue, $repl);

            return true;
        } else {
            return false;
        }
    }

    /**
     * This function checks attribute values for entity-encoded values
     * and returns them translated into 8-bit strings so we can run
     * checks on them.
     *
     * @param  string  $attvalue
     * @return void
     */
    public function tln_defang(&$attvalue)
    {
        /**
         * Skip this if there aren't ampersands or backslashes.
         */
        if (strpos($attvalue, '&') === false
            && strpos($attvalue, '\\') === false
        ) {
            return;
        }

        do {
            $m = false;
            $m = $m || $this->tln_deent($attvalue, '/\&#0*(\d+);*/s');
            $m = $m || $this->tln_deent($attvalue, '/\&#x0*((\d|[a-f])+);*/si', true);
            $m = $m || $this->tln_deent($attvalue, '/\\\\(\d+)/s', true);
        } while ($m == true);

        $attvalue = stripslashes($attvalue);
    }

    /**
     * Kill any tabs, newlines, or carriage returns. Our friends the
     * makers of the browser with 95% market value decided that it'd
     * be funny to make "java[tab]script" be just as good as "javascript".
     *
     * @param  string  $attvalue
     * @return void
     */
    public function tln_unspace(&$attvalue)
    {
        if (strcspn($attvalue, "\t\r\n\0 ") != strlen($attvalue)) {
            $attvalue = str_replace(
                ["\t", "\r", "\n", "\0", ' '],
                ['', '', '', '', ''],
                $attvalue
            );
        }
    }

    /**
     * This function runs various checks against the attributes.
     *
     * @param  string  $tagname
     * @param  array  $attary
     * @param  array  $rm_attnames
     * @param  array  $bad_attvals
     * @param  array  $add_attr_to_tag
     * @param  string  $trans_image_path
     * @param  bool  $block_external_images
     * @return array with modified attributes.
     */
    public function tln_fixatts(
        $tagname,
        $attary,
        $rm_attnames,
        $bad_attvals,
        $add_attr_to_tag,
        $trans_image_path,
        $block_external_images
    ) {
        /**
         * Convert to array if is not.
         */
        $attary = is_array($attary) ? $attary : [];

        foreach ($attary as $attname => $attvalue) {
            /**
             * See if this attribute should be removed.
             */
            foreach ($rm_attnames as $matchtag => $matchattrs) {
                if (preg_match($matchtag, $tagname)) {
                    foreach ($matchattrs as $matchattr) {
                        if (preg_match($matchattr, $attname)) {
                            unset($attary[$attname]);

                            continue 2;
                        }
                    }
                }
            }

            $this->tln_defang($attvalue);

            $this->tln_unspace($attvalue);

            /**
             * Now let's run checks on the attvalues.
             * I don't expect anyone to comprehend this. If you do,
             * get in touch with me so I can drive to where you live and
             * shake your hand personally. :)
             */
            foreach ($bad_attvals as $matchtag => $matchattrs) {
                if (preg_match($matchtag, $tagname)) {
                    foreach ($matchattrs as $matchattr => $valary) {
                        if (preg_match($matchattr, $attname)) {
                            [$valmatch, $valrepl] = $valary;

                            $newvalue = preg_replace($valmatch, $valrepl, $attvalue);

                            if ($newvalue != $attvalue) {
                                $attary[$attname] = $newvalue;
                                $attvalue = $newvalue;
                            }
                        }
                    }
                }
            }
        }

        /**
         * See if we need to append any attributes to this tag.
         */
        foreach ($add_attr_to_tag as $matchtag => $addattary) {
            if (preg_match($matchtag, $tagname)) {
                $attary = array_merge($attary, $addattary);
            }
        }

        return $attary;
    }

    /**
     * Fix url.
     *
     * @return void
     */
    public function tln_fixurl($attname, &$attvalue, $trans_image_path, $block_external_images)
    {
        $sQuote = '"';

        $attvalue = trim($attvalue);

        if ($attvalue && ($attvalue[0] == '"' || $attvalue[0] == "'")) {
            // remove the double quotes
            $sQuote = $attvalue[0];

            $attvalue = trim(substr($attvalue, 1, -1));
        }

        /**
         * Replace empty src tags with the blank image.  src is only used
         * for frames, images, and image inputs.  Doing a replace should
         * not affect them working as should be, however it will stop
         * IE from being kicked off when src for img tags are not set.
         */
        if ($attvalue == '') {
            $attvalue = $sQuote.$trans_image_path.$sQuote;
        } else {
            // first, disallow 8 bit characters and control characters
            if (preg_match('/[\0-\37\200-\377]+/', $attvalue)) {
                switch ($attname) {
                    case 'href':
                        $attvalue = $sQuote.'http://invalid-stuff-detected.example.com'.$sQuote;
                        break;

                    default:
                        $attvalue = $sQuote.$trans_image_path.$sQuote;
                        break;

                }
            } else {
                $aUrl = parse_url($attvalue);

                if (isset($aUrl['scheme'])) {
                    switch (strtolower($aUrl['scheme'])) {
                        case 'mailto':
                        case 'http':
                        case 'https':
                        case 'ftp':
                            if ($attname != 'href') {
                                if ($block_external_images == true) {
                                    $attvalue = $sQuote.$trans_image_path.$sQuote;
                                } else {
                                    if (! isset($aUrl['path'])) {
                                        $attvalue = $sQuote.$trans_image_path.$sQuote;
                                    }
                                }
                            } else {
                                $attvalue = $sQuote.$attvalue.$sQuote;
                            }
                            break;

                        case 'outbind':
                            $attvalue = $sQuote.$attvalue.$sQuote;
                            break;

                        case 'cid':
                            $attvalue = $sQuote.$attvalue.$sQuote;
                            break;

                        default:
                            $attvalue = $sQuote.$trans_image_path.$sQuote;
                            break;
                    }
                } else {
                    if (! isset($aUrl['path']) || $aUrl['path'] != $trans_image_path) {
                        $$attvalue = $sQuote.$trans_image_path.$sQuote;
                    }
                }
            }
        }
    }

    /**
     * Fix style.
     *
     * @return void
     */
    public function tln_fixstyle($body, $pos, $trans_image_path, $block_external_images)
    {
        $me = 'tln_fixstyle';

        $content = '';

        $sToken = '';

        $bSucces = false;

        $bEndTag = false;

        for ($i = $pos,$iCount = strlen($body); $i < $iCount; $i++) {
            $char = $body[$i];

            switch ($char) {
                case '<':
                    $sToken = $char;
                    break;
                case '/':
                    if ($sToken == '<') {
                        $sToken .= $char;

                        $bEndTag = true;
                    } else {
                        $content .= $char;
                    }

                    break;

                case '>':
                    if ($bEndTag) {
                        $sToken .= $char;

                        if (preg_match('/\<\/\s*style\s*\>/i', $sToken, $aMatch)) {
                            $newpos = $i + 1;

                            $bSucces = true;

                            break 2;

                        } else {
                            $content .= $sToken;
                        }

                        $bEndTag = false;
                    } else {
                        $content .= $char;
                    }
                    break;
                case '!':
                    if ($sToken == '<') {
                        if (isset($body[$i + 2]) && substr($body, $i, 3) == '!--') {
                            $i = strpos($body, '-->', $i + 3);

                            if (! $i) {
                                $i = strlen($body);
                            }

                            $sToken = '';
                        }
                    } else {
                        $content .= $char;
                    }
                    break;
                default:
                    if ($bEndTag) {
                        $sToken .= $char;
                    } else {
                        $content .= $char;
                    }
                    break;
            }
        }

        if (! $bSucces) {
            return [false, strlen($body)];
        }

        /**
         * First look for general BODY style declaration, which would be
         * like so:
         * body {background: blah-blah}
         * and change it to .bodyclass so we can just assign it to a <div>
         */
        $content = preg_replace("|body(\s*\{.*?\})|si", '.bodyclass\\1', $content);

        $trans_image_path = $trans_image_path;

        // first check for 8bit sequences and disallowed control characters
        if (preg_match('/[\16-\37\200-\377]+/', $content)) {
            $content = '<!-- style block removed by html filter due to presence of 8bit characters -->';

            return [$content, $newpos];
        }

        // remove @import line
        $content = preg_replace("/^\s*(@import.*)$/mi", "\n<!-- @import rules forbidden -->\n", $content);

        $content = preg_replace('/(\\\\)?u(\\\\)?r(\\\\)?l(\\\\)?/i', 'url', $content);

        preg_match_all("/url\s*\((.+)\)/si", $content, $aMatch);

        if (count($aMatch)) {
            $aValue = $aReplace = [];

            foreach ($aMatch[1] as $sMatch) {
                $urlvalue = $sMatch;
                $this->tln_fixurl('style', $urlvalue, $trans_image_path, $block_external_images);
                $aValue[] = $sMatch;
                $aReplace[] = $urlvalue;
            }

            $content = str_replace($aValue, $aReplace, $content);
        }

        /**
         * Remove any backslashes, entities, and extraneous whitespace.
         */
        $contentTemp = $content;
        $this->tln_defang($contentTemp);
        $this->tln_unspace($contentTemp);

        $match = ['/\/\*.*\*\//',
            '/expression/i',
            '/behaviou*r/i',
            '/binding/i',
            '/include-source/i',
            '/javascript/i',
            '/script/i',
            '/position/i'];

        $replace = ['', 'idiocy', 'idiocy', 'idiocy', 'idiocy', 'idiocy', 'idiocy', ''];

        $contentNew = preg_replace($match, $replace, $contentTemp);

        if ($contentNew !== $contentTemp) {
            $content = $contentNew;
        }

        return [$content, $newpos];
    }

    /**
     * Body to div.
     *
     * @return void
     */
    public function tln_body2div($attary, $trans_image_path)
    {
        $me = 'tln_body2div';

        $divattary = ['class' => "'bodyclass'"];

        $has_bgc_stl = $has_txt_stl = false;

        $styledef = '';

        if (is_array($attary) && count($attary) > 0) {
            foreach ($attary as $attname=>$attvalue) {
                $quotchar = substr($attvalue, 0, 1);

                $attvalue = str_replace($quotchar, '', $attvalue);

                switch ($attname) {
                    case 'background':
                        $styledef .= "background-image: url('$trans_image_path'); ";
                        break;

                    case 'bgcolor':
                        $has_bgc_stl = true;

                        $styledef .= "background-color: $attvalue; ";
                        break;

                    case 'text':
                        $has_txt_stl = true;

                        $styledef .= "color: $attvalue; ";
                        break;

                }
            }

            // Outlook defines a white bgcolor and no text color. This can lead to white text on a white bg with certain themes.
            if ($has_bgc_stl && ! $has_txt_stl) {
                $styledef .= 'color: #000000; ';
            }

            if (strlen($styledef) > 0) {
                $divattary['style'] = "\"$styledef\"";
            }
        }

        return $divattary;
    }

    /**
     * Sanitize.
     *
     * @param  string  $body
     * @param  array  $tag_list
     * @param  array  $rm_tags_with_content
     * @param  array  $self_closing_tags
     * @param  bool  $force_tag_closing
     * @param  array  $rm_attnames
     * @param  array  $bad_attvals
     * @param  array  $add_attr_to_tag
     * @param  string  $trans_image_path
     * @param  bool  $block_external_images
     * @return string
     */
    public function tln_sanitize(
        $body,
        $tag_list,
        $rm_tags_with_content,
        $self_closing_tags,
        $force_tag_closing,
        $rm_attnames,
        $bad_attvals,
        $add_attr_to_tag,
        $trans_image_path,
        $block_external_images
    ) {
        /**
         * Normalize rm_tags and rm_tags_with_content.
         */
        $rm_tags = array_shift($tag_list);

        @array_walk($tag_list, [$this, 'tln_casenormalize']);

        @array_walk($rm_tags_with_content, [$this, 'tln_casenormalize']);

        @array_walk($self_closing_tags, [$this, 'tln_casenormalize']);

        /**
         * See if tag_list is of tags to remove or tags to allow.
         * false  means remove these tags
         * true   means allow these tags
         */
        $curpos = 0;
        $open_tags = [];
        $trusted = '';
        $skip_content = false;

        /**
         * Take care of netscape's stupid javascript entities like
         * &{alert('boo')};
         */
        $body = preg_replace('/&(\{.*?\};)/si', '&amp;\\1', $body);

        while (($curtag = $this->tln_getnxtag($body, $curpos)) != false) {
            [$tagname, $attary, $tagtype, $lt, $gt] = $curtag;
            $free_content = substr($body, $curpos, $lt - $curpos);

            /**
             * Take care of <style>.
             */
            if ($tagname == 'style' && $tagtype == 1) {
                [$free_content, $curpos] =
                    $this->tln_fixstyle($body, $gt + 1, $trans_image_path, $block_external_images);

                if ($free_content != false) {
                    if (! empty($attary)) {
                        $attary = $this->tln_fixatts($tagname,
                            $attary,
                            $rm_attnames,
                            $bad_attvals,
                            $add_attr_to_tag,
                            $trans_image_path,
                            $block_external_images
                        );
                    }

                    $trusted .= $this->tln_tagprint($tagname, $attary, $tagtype);

                    if (isset($this->$free_content)) {
                        $trusted .= $this->$free_content;
                    }

                    $trusted .= $this->tln_tagprint($tagname, false, 2);
                }

                continue;
            }

            if ($skip_content == false) {
                $trusted .= $free_content;
            }

            if ($tagname != false) {
                if ($tagtype == 2) {
                    if ($skip_content == $tagname) {
                        $tagname = false;

                        $skip_content = false;
                    } else {
                        if ($skip_content == false) {
                            if ($tagname == 'body') {
                                $tagname = 'div';
                            }

                            if (isset($open_tags[$tagname]) &&
                                $open_tags[$tagname] > 0
                            ) {
                                $open_tags[$tagname]--;
                            } else {
                                $tagname = false;
                            }
                        }
                    }
                } else {
                    if (! $skip_content) {
                        if (
                            $tagtype == 1
                            && in_array($tagname, $self_closing_tags)
                        ) {
                            $tagtype = 3;
                        }

                        /**
                         * See if we should skip this tag and any content
                         * inside it.
                         */
                        if (
                            $tagtype == 1
                            && in_array($tagname, $rm_tags_with_content)
                        ) {
                            $skip_content = $tagname;
                        } else {
                            if ((
                                ! $rm_tags
                                && in_array($tagname, $tag_list))
                                || (
                                    $rm_tags
                                    && ! in_array($tagname, $tag_list)
                                )
                            ) {
                                $tagname = false;
                            } else {
                                /**
                                 * Convert body into div.
                                 */
                                if ($tagname == 'body') {
                                    $tagname = 'div';
                                    $attary = $this->tln_body2div($attary, $trans_image_path);
                                }

                                if ($tagtype == 1) {
                                    if (isset($open_tags[$tagname])) {
                                        $open_tags[$tagname]++;
                                    } else {
                                        $open_tags[$tagname] = 1;
                                    }
                                }

                                /**
                                 * This is where we run other checks.
                                 */
                                if (is_array($attary) && count($attary) > 0) {
                                    $attary = $this->tln_fixatts(
                                        $tagname,
                                        $attary,
                                        $rm_attnames,
                                        $bad_attvals,
                                        $add_attr_to_tag,
                                        $trans_image_path,
                                        $block_external_images
                                    );
                                }
                            }
                        }
                    }
                }

                if ($tagname != false && $skip_content == false) {
                    $trusted .= $this->tln_tagprint($tagname, $attary, $tagtype);
                }
            }

            $curpos = $gt + 1;
        }

        $trusted .= substr($body, $curpos, strlen($body) - $curpos);

        if ($force_tag_closing == true) {
            foreach ($open_tags as $tagname => $opentimes) {
                while ($opentimes > 0) {
                    $trusted .= '</'.$tagname.'>';

                    $opentimes--;
                }
            }
            $trusted .= "\n";
        }

        return $trusted;
    }

    /**
     * HTML filter.
     *
     * @param  bool  $block_external_images
     * @return void
     */
    public function process($body, $trans_image_path, $block_external_images = false)
    {
        $tag_list = [
            false,
            'object',
            'meta',
            'html',
            'head',
            'base',
            'link',
            'frame',
            'iframe',
            'plaintext',
            'marquee',
        ];

        $rm_tags_with_content = [
            'script',
            'applet',
            'embed',
            'title',
            'frameset',
            'xmp',
            'xml',
        ];

        $self_closing_tags = [
            'img',
            'br',
            'hr',
            'input',
            'outbind',
        ];

        $force_tag_closing = true;

        $rm_attnames = [
            '/.*/' => [
                '/^on.*/i',
                '/^dynsrc/i',
                '/^data.*/i',
                '/^lowsrc.*/i',
            ],
        ];

        $bad_attvals = [
            '/.*/' => [
                '/^src|background/i' => [
                    [
                        '/^([\'"])\s*\S+script\s*:.*([\'"])/si',
                        '/^([\'"])\s*mocha\s*:*.*([\'"])/si',
                        '/^([\'"])\s*about\s*:.*([\'"])/si',
                    ], [
                        "\\1$trans_image_path\\2",
                        "\\1$trans_image_path\\2",
                        "\\1$trans_image_path\\2",
                    ],
                ],

                '/^href|action/i' => [
                    [
                        '/^([\'"])\s*\S+script\s*:.*([\'"])/si',
                        '/^([\'"])\s*mocha\s*:*.*([\'"])/si',
                        '/^([\'"])\s*about\s*:.*([\'"])/si',
                    ], [
                        '\\1#\\1',
                        '\\1#\\1',
                        '\\1#\\1',
                    ],
                ],
            ],
        ];

        if ($block_external_images) {
            array_push(
                $bad_attvals['/.*/']['/^src|background/i'][0],
                '/^([\'\"])\s*https*:.*([\'\"])/si'
            );

            array_push(
                $bad_attvals['/.*/']['/^src|background/i'][1],
                "\\1$trans_image_path\\1"
            );
        }

        $add_attr_to_tag = [
            '/^a$/i' => ['target' => '"_blank"'],
        ];

        $trusted = $this->tln_sanitize(
            $body,
            $tag_list,
            $rm_tags_with_content,
            $self_closing_tags,
            $force_tag_closing,
            $rm_attnames,
            $bad_attvals,
            $add_attr_to_tag,
            $trans_image_path,
            $block_external_images
        );

        return $trusted;
    }
}
