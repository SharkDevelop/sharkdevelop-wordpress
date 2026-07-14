<?php

class Ezfc_Element_Hidden extends Ezfc_Element {
	public function get_output() {
		global $product;
		
		$el_text = "";

		$add_attr = "data-initvalue='" . esc_attr($this->data->value) . "'";

		// use woocommerce product price
		//if (!empty($this->data->use_woocommerce_price)) $add_attr .= " data-use_woocommerce_price='1'";
		if (!empty($this->data->use_woocommerce_price) && !empty($product) && method_exists($product, "get_price")) $this->data->value = $this->frontend->normalize_value($product->get_price(), true);
		// readonly
		if (!empty($this->data->read_only)) $add_attr .= " readonly";
		// max length
		if (isset($this->data->max_length) && $this->data->max_length != "") $add_attr .= " maxlength='{$this->data->max_length}'";

		$el_text .= "<input class='{$this->data->class} ezfc-element ezfc-element-input-hidden' {$this->output["factor"]} id='{$this->output["element_child_id"]}' name='{$this->output["element_name"]}' type='hidden' value='{$this->data->value}' {$this->output["style"]} {$add_attr} />";

		return $el_text;
	}

	/**
		returns the formatted submitted value
	**/
	public function get_summary_value_formatted() {
		return "";
	}
}