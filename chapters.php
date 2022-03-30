<?php
// Chapters API endpoint. Queries the graphql api for
// chapters where the bookId matches the select dropdown.

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

// Build the GraphQL query
$builder = (new QueryBuilder())
    ->setVariable('bookId', 'Int', true)
    ->selectField(
        (new QueryBuilder('chapters'))
            ->setArgument('where', new RawObject('{ book: { id: { _eq: $bookId } } }'))
            ->selectField('id')
            ->selectField('chapterNumber')
    );
$gql = $builder->getQuery();

try {
    // Set the variable from the book select and grab the results
    $variablesArray = ['bookId' => $_GET['id']];
    $results = $client->runQuery($gql, true, $variablesArray);
} catch (QueryError $exception) {
    // Catch query error and display error details
    print_r($exception->getErrorDetails());
    exit;
}

// Print the results as json
echo json_encode($results->getData()['chapters']);
