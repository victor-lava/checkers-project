<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{

    public $gameTable;
    public $className;
    public $name;

    public function firstPlayer() {
        return $this->hasOne('App\User', 'id', 'first_user_id');
    }

    public function secondPlayer() {
        return $this->hasOne('App\User', 'id', 'second_user_id');
    }

    public function isOngoing() {
        return $this->status === 1 ? true : false;
    }

    public function badge() {
        $this->className = $this->status === 0 ? 'warning' : 'success';
        $this->name = $this->getStatus();
        return $this;
    }
    public function button() {
        $this->className = $this->status === 0 ? 'success' : 'primary';
        $this->name = $this->status === 0 ? 'Play' : 'Watch';
        return $this;
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

    public function findChecker(object $checkers, int $x, int $y) {
        $result = false;

        foreach ($checkers as $checker) {
            if($checker->x === $x && $checker->y === $y) { $result = $checker; break; }
        }

        return $result;
    }

    public function getGameTable($checkers) {
        $this->createTable();
        for ($y = 0; $y < count($this->gameTable); $y++) { // y
            for ($x = 0; $x < count($this->gameTable); $x++) {
                $this->gameTable[$y][$x]['checker'] = $this->findChecker($checkers, $x, $y);
            }
        }
        return $this->gameTable;
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

    public function getDuration() {

        $start = new \DateTime($this->date_started);
        $current = new \DateTime();

        $difference = $current->diff($start);
        $minutes = ($difference->h * 60) + $difference->i;
    
        return $minutes . ' min. ' . $difference->s . ' s.';
    }

    public function createCheckers() {
        for ($y = 0; $y < count($this->gameTable); $y++) { // y
            for ($x = 0; $x < count($this->gameTable); $x++) {
                $checker = false;
                if(($y <= 2 || $y >= 5) && $this->gameTable[$x][$y]['color'] === 1) {
                    $checker = new Checker();
                    $checker->game_id = $this->id;
                    $checker->user_id = null;
                    $checker->position_name = $this->gameTable[$y][$x]['position'];
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

    public function create() {
        $this->createTable();
        $this->createCheckers();
    }
}
