<?php

// Autoload Composer packages
require __DIR__ . '/vendor/autoload.php';

use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\QueryBuilder\QueryBuilder;
use GraphQL\RawObject;

$client = new Client(
    'https://lds-scripture-api.herokuapp.com/v1/graphql'
);

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
    $variablesArray = ['bookId' => $_GET['id']];
    $results = $client->runQuery($gql, true, $variablesArray);
} catch (QueryError $exception) {
    // Catch query error and display error details
    print_r($exception->getErrorDetails());
    exit;
}

// Reformat the results to an array and get the results of part of the array
// $results->reformatResults(true);

echo json_encode($results->getData()['chapters']);
