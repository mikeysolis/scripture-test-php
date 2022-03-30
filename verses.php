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
    ->setVariable('chapterId', 'smallint', true)
    ->selectField(
        (new QueryBuilder('verses'))
            ->setArgument('where', new RawObject('{ chapterId: { _eq: $chapterId } }'))
            ->selectField('id')
            ->selectField('verseNumber')
    );
$gql = $builder->getQuery();

try {
    $variablesArray = ['chapterId' => $_GET['id']];
    $results = $client->runQuery($gql, true, $variablesArray);
} catch (QueryError $exception) {
    // Catch query error and desplay error details
    print_r($exception->getErrorDetails());
    exit;
}

echo json_encode($results->getData()['verses']);
