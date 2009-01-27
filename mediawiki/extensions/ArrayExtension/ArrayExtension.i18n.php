<?php
 
/**
 * Get translated magic words, if available
 *
 * @param string $lang Language code
 * @return array
 */
function efArrayExtensionWords( $lang ) {
        $words = array();
 
        /**
         * English
         */
        $words['en'] = array(
                'arraydefine'    => array( 0, 'arraydefine' ),

                'arraysize'         => array( 0,'arraysize' ),
                'arrayprint'          => array( 0, 'arrayprint' ),

                'arrayunique'         => array( 0,'arrayunique' ),
                'arraysort'         => array( 0,'arraysort' ),
                'arraymerge'         => array( 0,'arraymerge' ),
                'arraymember'         => array( 0,'arraymember' ),	

                'arrayunion'         => array( 0,'arrayunion' ),
                'arrayintersect'         => array( 0,'arrayintersect' ),
                'arraydiff'         => array( 0,'arraydiff' ),	

                'arraypush'         => array( 0,'arraypush' ),
                'arraypop'         => array( 0,'arraypop' ),
        );
 
        # English is used as a fallback, and the English synonyms are
        # used if a translation has not been provided for a given word
        return ( $lang == 'en' || !isset( $words[$lang] ) )
                ? $words['en']
                : array_merge( $words['en'], $words[$lang] );
}
 
?>