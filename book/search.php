<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// include database and object files
include_once '../config/database.php';
include_once '../objects/book.php';
  
// instantiate database and book object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$book = new Book($db);
  
// read books will be here
$stmt = $book->read(true);
$numrows = $stmt->fetchColumn();
$rowsperpage = 25;
// find out total pages
$totalpages = ceil($numrows / $rowsperpage);

if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
    $currentpage = (int) $_GET['currentpage'];
} else {
    $currentpage = 1;  // default page number
}

// if current page is greater than total pages
if ($currentpage > $totalpages) {
// set current page to last page
    $currentpage = $totalpages;
}
// if current page is less than first page
if ($currentpage < 1) {
// set current page to first page
    $currentpage = 1;
}
// the offset of the list, based on current page
$offset = ($currentpage - 1) * $rowsperpage;

// check if more than 0 record found
if($numrows>0){
	$stmt = $book->read(false, $offset, $rowsperpage);

    // books array
    $books_arr=array();
    array_push($books_arr, array("number_of_books" => $numrows));
  
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        extract($row);
  
        $bookItem=array(
            "id" => $id,
            "title" => $title,
            "download_links" => $download_links,
            "authors" => $authors,
            "subjects" => $subjects,
            "language" => $languages,
            "bookshelves" => $bookshelves
        );
  
        array_push($books_arr, $bookItem);
    }
  
    // set response code - 200 OK
    http_response_code(200);
  
    // show books data in json format
    echo json_encode($books_arr, JSON_UNESCAPED_SLASHES);
}
else{
  
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no books found
    echo json_encode(
        array("message" => "No books found.")
    );
}
?>