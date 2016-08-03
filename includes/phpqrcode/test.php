<?php    
/*
 * PHP QR Code encoder
 *
 * Exemplatory usage
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
    
  
    
    //set it to writable location, a place for temp generated PNG files

	$data='http://asdfasdf.com';

    include "qrlib.php";
    if (!empty($data)) 
	{      
        $filename = './temp/test.png';
		if (!file_exists($filename))
		{
        	QRcode::png($data, $filename, 'L', 10, 2);  
		}        
    } 
        
    //display generated file
    echo '<img src="'.$filename.'" /><hr/>';  
    
   ?>
    