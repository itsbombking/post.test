<?php

class Test extends CI_Controller {
{
	public function checkdb()
	{
		$conn = pg_connect("host=localhost dbname=post.test user=postgres password=toor");

		if (!$conn) {
			echo "Connection failed";
		} else {
			echo "Connected";
		}

	}
}
