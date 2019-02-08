<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{

    public $gameTable;

    public function firstPlayer() {
        return $this->hasOne('App\User', 'id', 'first_user_id');
    }

    public function secondPlayer() {
        return $this->hasOne('App\User', 'id', 'second_user_id');
    }

    public function isOngoing() {
        return $this->status === 1 ? true : false;
    }

    public function getStatus() {
        $status = '';
        switch ($this->status) {
            case 0:
                $status = 'Waiting';
                break;

            case 1:
                $status = 'Ongoing';
                break;

            case 2:
                $status = 'Completed';
                break;

        }
        return $status;
    }

    public function createGame() {

    }

    public function createTable() {
        $squares = [];
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $alphabet = str_split($alphabet);
        $size = 8;

        for ($y = 0; $y < $size; $y++) { // y
            $rowNumber = $size - $y;
            $isYEven = $y % 2 ? true : false;
            $squares[$y] = [];

            for ($x = 0; $x < $size ; $x++) {
                $isXEven = $x % 2 ? true : false;
                $positionName = $alphabet[$x] . $rowNumber;
                $checker = false;

                if( ($isYEven == true && $isXEven == true) ||
                    ($isYEven == false && $isXEven == false)) { // White Squares
                        $color = 0;
                }
                elseif( ($isYEven == true && $isXEven == false) ||
                        ($isYEven == false && $isXEven == true)) { // Black Squares
                        $color = 1;

                        // 1. case kai kuriame lentelę naujai, tai kuriame pagal $y <= 2 || $y >= 5 taisykles
                        // if($y <= 2 || $y >= 5) {
                        //     $checker = new Checker();
                        //     $checker->game_id = $gameID;
                        //     $checker->user_id = null;
                        //     $checker->position_name = $positionName;
                        //     $checker->x = $x;
                        //     $checker->y = $y;
                        //     if($y <= 2) { $checker->color = 1; } // Black checker
                        //     elseif($y >= 5) { $checker->color = 0; } // White checker
                        //     $checker->save();
                        // }

                        // 2. case kai gauname lentelę iš duomenų bazės ir koordinatės yra sumaisytos, tada nekuriame checkeriu, bet juos uzfiliname i masyva
                        // $checker = $this->findChecker($checkers, $x, $y);
                }

                $squares[$y][$x]['color'] = $color;
                $squares[$y][$x]['position'] = $positionName;
                $squares[$y][$x]['checker'] = $checker;
            }

        }
        $this->gameTable = $squares;
        // return $squares;
    }

    public function createCheckers() {
        for ($y = 0; $y < count($this->gameTable); $y++) { // y
            for ($x = 0; $x < count($this->gameTable); $x++) {
                $checker = false;
                if(($y <= 2 || $y >= 5) && $this->gameTable[$x][$y]['color'] === 1) {
                    $checker = new Checker();
                    $checker->game_id = $this->id;
                    $checker->user_id = null;
                    $checker->position_name = $this->gameTable[$x][$y]['position'];
                    $checker->x = $x;
                    $checker->y = $y;
                    if($y <= 2) { $checker->color = 1; } // Black checker
                    elseif($y >= 5) { $checker->color = 0; } // White checker
                    $checker->save();
                }
                $this->gameTable[$y][$x]['checker'] = $checker;
            }
        }
        return $this->gameTable;
    }

    public function start() {
        $this->createTable();
        $this->createCheckers();
    }
}
