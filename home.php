<?php
// Home page of the app

// Autoload Composer packages
require __DIR__ . '/vendor/autoload.php';

// GraphQL imports
use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\QueryBuilder\QueryBuilder;
use GraphQL\RawObject;

// Set up the GraphQL client
$client = new Client(
    'https://lds-scripture-api.herokuapp.com/v1/graphql'
);

// Function: getBooks()
// @Param: $client - the GraphQL client to fetch from
// @Param: $volumeId - in this case we only want books from volume 2
// This function builds a graphql query and runs it, returning the results
// in array form.
function getBooks($client, $volumeId = 2)
{
    // Build the GraphQL query
    $builder = (new QueryBuilder())
        ->setVariable('volumeId', 'Int', true)
        ->selectField(
            (new QueryBuilder('books'))
                ->setArgument('where', new RawObject('{ volume: { id: { _eq: $volumeId } } }'))
                ->selectField('id')
                ->selectField('bookTitle')
        );
    $gql = $builder->getQuery();

    try {
        // Set the variable from the book select and grab the results
        $variablesArray = ['volumeId' => $volumeId];
        $results = $client->runQuery($gql, true, $variablesArray);
    } catch (QueryError $exception) {
        // Catch query error and display error details
        print_r($exception->getErrorDetails());
        exit;
    }

    // Reformat the results to an array and get the results of part of the array
    $results->reformatResults(true);

    return $results->getData()['books'];
}

$initial_books = getBooks($client);

?>

<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Scripture API PHP Test</title>
    <style type="text/css">
      <?php include 'css/normalize.css';?>
      <?php include 'css/skeleton.css';?>
      .container,
      .search-result {
        margin-top: 5rem;
      }
    </style>
  </head>
  <body>
  <div class="App">
      <div class="container">
        <div class="row">
          <div class="twelve columns">
            <h3>PHP API Example</h3>
            <p>
              This example uses PHP to pull data from a GraphQL scripture API.
              The user may search the scriptures and the result is printed to
              the page.
            </p>
          </div>
        </div>

        <div class="row">
          <div class="one-third column">
            <label htmlFor="book">Book</label>
            <select
      class="u-full-width"
      name="book"
      id="book"
      placeholder="Select a book"
    >
      <option value="">Select a book</option>
      <?php
foreach ($initial_books as $book) {
    echo '<option value="' . $book['id'] . '">' . $book['bookTitle'] . '</option>';
}
?>
    </select>
          </div>
          <div class="one-third column">
            <label htmlFor="chapter">Chapter</label>
            <select
      class="u-full-width"
      name="chapter"
      id="chapter"
      placeholder="Select a chapter"
     >
      <option>Select a chapter</option>
    </select>
          </div>
          <div class="one-third column">
            <label htmlFor="verse">Verse</label>
            <select
      class="u-full-width"
      name="verse"
      id="verse"
      placeholder="Select a verse">
      <option>Select a verse</option>
    </select>
          </div>
        </div>

        <div class="row">
          <div class="twelve columns">
            <h5 class="search-result">Search Result</h5>
            <div id="verse">
                <p>Select a Book, then a Chapter and finally a Verse</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
      $( "select[name='book']" ).change(function () {
        var bookId = $(this).val();

        if(bookId) {
            $.ajax({
                url: "chapters.php",
                dataType: 'Json',
                data: {'id': bookId},
                success: function(data) {
                    $('select[name="chapter"]').empty();
                    $('select[name="chapter"]').append('<option value="">Select a chapter</option>');
                    $('select[name="verse"]').empty();
                    $('select[name="verse"]').append('<option value="">Select a verse</option>');
                    $.each(data, function(key, value) {
                        $('select[name="chapter"]').append('<option value="'+ value['id'] +'">'+ value['chapterNumber'] +'</option>');
                    });
                }
            });
        }else{
            $('select[name="chapter"]').empty();
        }
    });

    $( "select[name='chapter']" ).change(function () {
        var chapterId = $(this).val();

        if(chapterId) {
            $.ajax({
                url: "verses.php",
                dataType: 'Json',
                data: {'id': chapterId},
                success: function(data) {
                    $('select[name="verse"]').empty();
                    $('select[name="verse"]').append('<option value="">Select a verse</option>');
                    $.each(data, function(key, value) {
                        $('select[name="verse"]').append('<option value="'+ value['id'] +'">'+ value['verseNumber'] +'</option>');
                    });
                }
            });
        }else{
            $('select[name="verse"]').empty();
        }
    });

    $( "select[name='verse']" ).change(function () {
        var verseId = $(this).val();

        if(verseId) {
            $.ajax({
                url: "verse.php",
                dataType: 'Json',
                data: {'id': verseId},
                success: function(data) {
                    $.each(data, function(key, value) {
                        $('div[id="verse"]').empty();
                        $('div[id="verse"]').append('<p>' + value['verseNumber'] + ' ' + value['scriptureText'] + '</p>');
                    });
                }
            });
        }else{
            $('div[id="verse"]').append('<p>Select a Book, then a Chapter and finally a Verse</p>');
        }
    });
    </script>
  </body>
</html>