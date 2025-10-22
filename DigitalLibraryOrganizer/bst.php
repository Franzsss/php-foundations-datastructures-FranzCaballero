<?php
// Class representing a single node in the BST
class Node {
    public $data;
    public $left;
    public $right;

    public function __construct($data) {
        $this->data = $data;
        $this->left = null;
        $this->right = null;
    }
}

// Class representing the Binary Search Tree
class BST {
    public $root;

    public function __construct() {
        $this->root = null;
    }

    // Public method to start the insertion process
    public function insert($data) {
        $this->root = $this->insertRec($this->root, $data);
    }

    // Recursive insertion logic
    private function insertRec($node, $data) {
        if ($node === null) {
            return new Node($data);
        }
        // Use case-insensitive string comparison (strcasecmp) for alphabetical ordering
        if (strcasecmp($data, $node->data) < 0) {
            $node->left = $this->insertRec($node->left, $data);
        } elseif (strcasecmp($data, $node->data) > 0) {
            $node->right = $this->insertRec($node->right, $data);
        }
        return $node;
    }

    // Public method to get an in-order traversal (alphabetical list)
    public function inorder(&$result = []) {
        $this->inorderRec($this->root, $result);
        return $result;
    }

    // Recursive in-order traversal logic
    private function inorderRec($node, &$result) {
        if ($node === null) return;
        $this->inorderRec($node->left, $result);
        $result[] = $node->data; // Add node data to result array
        $this->inorderRec($node->right, $result);
    }
}
