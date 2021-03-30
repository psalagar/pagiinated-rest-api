<?php
class Book{
  
    // database connection and table name
    private $conn;
    private $tableName = "books_book";
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
    // read books
    function read($getCount = false, $offset =1, $rowsperpage = 0){    
        // select all query
	    if ($getCount == true) {
	    	$query = "SELECT COUNT(*)";
	    } else {
		    $query = "SELECT books_book.id, books_book.title, 
		       		   format.links as download_links,
					   author.authors,
					   subject.subjects,
					   blanguage.languages,
					   bookshelf.bookshelves";
		}
		$query .= " FROM public.books_book
		LEFT JOIN (SELECT book_id, STRING_AGG(url, ',') AS links
	           FROM books_format
	           GROUP BY book_id) format ON books_book.id = format.book_id
		LEFT JOIN (SELECT book_id, STRING_AGG(books_author.name, ',') AS authors
	           FROM books_book_authors
			   INNER JOIN books_author ON books_author.id = books_book_authors.author_id
	           GROUP BY book_id) author ON books_book.id = author.book_id
	    LEFT JOIN (SELECT book_id, STRING_AGG(books_subject.name, ',') AS subjects
			   FROM books_book_subjects
			   INNER JOIN books_subject ON books_subject.id = books_book_subjects.subject_id
			   GROUP BY book_id) subject ON books_book.id = subject.book_id
	    LEFT JOIN (SELECT book_id, STRING_AGG(books_language.code, ',') AS languages
			   FROM books_book_languages
			   INNER JOIN books_language ON books_language.id = books_book_languages.language_id
			   GROUP BY book_id) blanguage ON books_book.id = blanguage.book_id
	    LEFT JOIN (SELECT book_id, STRING_AGG(books_bookshelf.name, ',') AS bookshelves
			   FROM books_book_bookshelves 
			   INNER JOIN books_bookshelf ON books_bookshelf.id = books_book_bookshelves.bookshelf_id
			   GROUP BY book_id) bookshelf ON books_book.id = bookshelf.book_id";
	    $query .= " WHERE books_book.title IS NOT NULL";
	    //Filter search data
		if (isset($_GET['bookname']) && $_GET['bookname'] != '') {
			$query .= " AND books_book.title ILIKE '%".$_GET['bookname']."%' ";
		}
		if (isset($_GET['authorname']) && $_GET['authorname'] != '') {
			$query .= " AND author.authors ILIKE '%".$_GET['authorname']."%' ";
		}
		if (isset($_GET['subject']) && $_GET['subject'] != '') {
			$query .= " AND subject.subjects ILIKE '%".$_GET['subject']."%' ";
		}
		if (isset($_GET['language']) && $_GET['language'] != '') {
			$query .= " AND blanguage.languages ILIKE '%".$_GET['language']."%' ";
		}
		if (isset($_GET['bookshelf']) && $_GET['bookshelf'] != '') {
			$query .= " AND bookshelf.bookshelves ILIKE '%".$_GET['bookshelf']."%' ";
		}
		$query .= ($getCount == false)? " OFFSET $offset LIMIT $rowsperpage": '';
		
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }
}
?>