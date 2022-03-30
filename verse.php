<?php
// Verse API endpoint. Queries the graphql api for
// verse where the verseId matches the select dropdown.

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
    ->setVariable('verseId', 'Int', true)
    ->selectField(
        (new QueryBuilder('verses'))
            ->setArgument('where', new RawObject('{ id: { _eq: $verseId } }'))
            ->selectField('verseNumber')
            ->selectField('scriptureText')
    );
$gql = $builder->getQuery();

try {
    // Set the variable from the verse select and grab the results
    $variablesArray = ['verseId' => $_GET['id']];
    $results = $client->runQuery($gql, true, $variablesArray);
} catch (QueryError $exception) {
    // Catch query error and desplay error details
    print_r($exception->getErrorDetails());
    exit;
}

// Print the results as json
echo json_encode($results->getData()['verses']);
