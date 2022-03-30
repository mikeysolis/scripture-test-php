<?php
// Verses API endpoint. Queries the graphql api for
// verses where the chapterId matches the select dropdown.

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
    ->setVariable('chapterId', 'smallint', true)
    ->selectField(
        (new QueryBuilder('verses'))
            ->setArgument('where', new RawObject('{ chapterId: { _eq: $chapterId } }'))
            ->selectField('id')
            ->selectField('verseNumber')
    );
$gql = $builder->getQuery();

try {
    // Set the variable from the chapter select and grab the results
    $variablesArray = ['chapterId' => $_GET['id']];
    $results = $client->runQuery($gql, true, $variablesArray);
} catch (QueryError $exception) {
    // Catch query error and desplay error details
    print_r($exception->getErrorDetails());
    exit;
}

// Print the results as json
echo json_encode($results->getData()['verses']);
