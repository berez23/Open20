<?php

/**
 *
 * Function code for the matrix cofactors() function
 *
 */

namespace Matrix;

/**
 * Returns the cofactors of a matrix or an array.
 *
 * @param Matrix|array $matrix Matrix or an array to treat as a matrix.
 * @return Matrix The new matrix
 * @throws Exception If argument isn't a valid matrix or array.
 */
function cofactors($matrix)
{
    if (is_array($matrix)) {
        $matrix = new Matrix($matrix);
    }
    if (!$matrix instanceof Matrix) {
        throw new Exception('Must be Matrix or array');
    }

    return Functions::cofactors($matrix);
}
