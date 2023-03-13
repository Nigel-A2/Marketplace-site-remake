<?php

// two sum

/**
     * @param Integer[] $nums
     * @param Integer $target
     * @return Integer[]
     */
    function twoSum($nums, $target) {
        /*
        // this part I do not get, will need ChatGPT to explain
        $res = [];
        $setSum = [];
        
        for($i = 0; $i < sizeof($nums); $i++)
        {
            $diff = $target - $nums[$i];
            if(array_search($diff, $setSum) !== false)
            {
                $key = array_search($diff, $setSum);
                $res[] = $i;
                $res[] = $key;
            }
            $setSum[] = $nums[$i];
        }
        return $res;
        */

        $nums = [2,7,11,15];
        $target = 9;
        
        for($i = 0; $i < sizeof($nums); $i++)
        {
            for($j = $i + 1; $j < sizeof($nums); $j++)
            {
                if ($nums[$i] + $nums[$j] == $target)
                {
                    //return [$i, $j];
                    echo [$i, $j];
                }
            }
        }
    }

// three sum 



// three sum closest


 
// four sum



// word ladder



// rotate array



// isomorphic strings



// palindrome numbers



// median of two sorted arrays



// Kth largest element in an array



// wildcard matching

function isMatch($s, $p) {
    $m = strlen($s); // length of string
    $n = strlen($p); // length of pattern
    $dp = array_fill(0, $m + 1, array_fill(0, $n + 1, false));
    $dp[0][0] = true;
    for($j = 1; $j <= $n; $j++){
        //  '*' Matches any sequence of characters (including the empty sequence).
        // $p[$j-1] is the index of the value in the pattern array, starting from 0
      if($p[$j-1] == '*') $dp[0][$j] = $dp[0][$j-1];
    }
    for($i = 1; $i <= $m; $i++){
      for($j = 1; $j <= $n; $j++){
        if($p[$j-1] == '?' || $s[$i-1] == $p[$j-1]){
          $dp[$i][$j] = $dp[$i-1][$j-1];
        } else if($p[$j-1] == '*'){
          $dp[$i][$j] = $dp[$i-1][$j] || $dp[$i][$j-1];
        }
      }
    }
    return $dp[$m][$n];
  }

  /*
  The function uses dynamic programming to compute a table of Boolean values representing whether substrings
   of the input string and pattern match up to the corresponding indices.
    The table is initialized with false values, except for dp[0][0], which is initialized to true.
     Then, the function iterates over the substrings of the input string and pattern,
      updating the table based on the current characters of the pattern and input string.
       If the current characters match or the current pattern character is '?',
        the table value is copied from the diagonal element (representing a match without considering the current characters).
         If the current pattern character is '*', the table value is set to true if either the element above
          (representing a match with the current character) or the element to the left
           (representing a match without the current character) is true.
            Finally, the function returns the value of dp[m][n], representing whether the entire input string and pattern match.
  */
// regular expression matching



// merge intervals



// insert interval



// evaluate reverse polish notation



// integer to roman numerals



// roman to integer



// merge sorted array



// string to integer (atoi)



// rotate array