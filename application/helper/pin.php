<?php

	function generatePin($length){
		$characters = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9'];

		$pin = '';
		for ($i = 0; $i < $length; $i++){
			$pin .= $characters[rand(0,count($characters)-1)];
		}
		return $pin;
	}

