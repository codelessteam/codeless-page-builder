<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$output = '';

$atts = cl_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$extra_class = '';

$row_id = 'cl_row_inner_' . str_replace( ".", '-', uniqid("", true) );

$output .= '<div id="'.esc_attr( $row_id ).'" class="cl-row_inner '.esc_attr( $extra_class ).' cl-element '.esc_attr( $this->generateClasses('.cl-row_inner') ).'" '.$this->generateStyle('.cl-row_inner').'>';

        $output .= '<div class="row cl_row-sortable">';
            $output .= cl_remove_wpautop($content);
        $output .= '</div>';

$output .= '</div>';


if( $inner_columns_gap != '15' ){
	$this->addCustomCss( '#'.esc_attr( $row_id ).' .cl_cl_column_inner, #'.esc_attr( $row_id ).' .row > .cl_column_inner{ padding-left: '. esc_attr( $inner_columns_gap ) .'px; padding-right: '. esc_attr($inner_columns_gap) .'px; }' );

	if( $inner_columns_gap == '0' ){
		$this->addCustomCss( '#'.esc_attr( $row_id ).' .row { margin:0; }' );
	}
}


echo cl_remove_wpautop( $output );