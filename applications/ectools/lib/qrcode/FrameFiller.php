<?php
/*
 * PHP QR Code encoder
 *
 * Main encoder classes.
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
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

class ectools_qrcode_FrameFiller {

    public $width;
    public $frame;
    public $x;
    public $y;
    public $dir;
    public $bit;

    //----------------------------------------------------------------------
    public function __construct($width, &$frame)
    {
        $this->width = $width;
        $this->frame = $frame;
        $this->x = $width - 1;
        $this->y = $width - 1;
        $this->dir = -1;
        $this->bit = -1;
    }

    //----------------------------------------------------------------------
    public function setFrameAt($at, $val)
    {
        $this->frame[$at['y']][$at['x']] = chr($val);
    }

    //----------------------------------------------------------------------
    public function getFrameAt($at)
    {
        return ord($this->frame[$at['y']][$at['x']]);
    }

    //----------------------------------------------------------------------
    public function next()
    {
        do {

            if($this->bit == -1) {
                $this->bit = 0;
                return array('x'=>$this->x, 'y'=>$this->y);
            }

            $x = $this->x;
            $y = $this->y;
            $w = $this->width;

            if($this->bit == 0) {
                $x--;
                $this->bit++;
            } else {
                $x++;
                $y += $this->dir;
                $this->bit--;
            }

            if($this->dir < 0) {
                if($y < 0) {
                    $y = 0;
                    $x -= 2;
                    $this->dir = 1;
                    if($x == 6) {
                        $x--;
                        $y = 9;
                    }
                }
            } else {
                if($y == $w) {
                    $y = $w - 1;
                    $x -= 2;
                    $this->dir = -1;
                    if($x == 6) {
                        $x--;
                        $y -= 8;
                    }
                }
            }
            if($x < 0 || $y < 0) return null;

            $this->x = $x;
            $this->y = $y;

        } while(ord($this->frame[$y][$x]) & 0x80);

        return array('x'=>$x, 'y'=>$y);
    }

};
