<?php

/**
 * BLS SDK for PHP example.
 *
 * @author Luke Carrier <luke.carrier@avadolearning.com>
 * @copyright 2017 AVADO Learning
 */

use AvadoLearning\BusinessLogicServices\RequestPreprocessor\BlsAuthTokenRequestPreprocessor;
use AvadoLearning\BusinessLogicServices\RequestPreprocessor\WsaRequestPreprocessor;
use AvadoLearning\BusinessLogicServices\SalesLogixService\ArrayOfContact;
use AvadoLearning\BusinessLogicServices\SalesLogixService\ArrayOfstring;
use AvadoLearning\BusinessLogicServices\SalesLogixService\Contact;
use AvadoLearning\BusinessLogicServices\SalesLogixService\ContactGender;
use AvadoLearning\BusinessLogicServices\SalesLogixService\ContactTitle;
use AvadoLearning\BusinessLogicServices\SalesLogixService\ContactType;
use AvadoLearning\BusinessLogicServices\SalesLogixService\CreateLead;
use AvadoLearning\BusinessLogicServices\SalesLogixService\CreateLeadEntitiesBrandAssociation;
use AvadoLearning\BusinessLogicServices\SalesLogixService\CreateLeadEntitiesCreateLeadRequest;
use AvadoLearning\BusinessLogicServices\SalesLogixService\CreateLeadEntitiesLeadSource;
use AvadoLearning\BusinessLogicServices\SalesLogixService\CreateLeadEntitiesSubmissionType;
use AvadoLearning\BusinessLogicServices\SoapClientFactory;

require_once __DIR__ . '/vendor/autoload.php';

// These values should come from your configuration
define('BLS_URL',        'https://instance.domain.com');
define('BLS_AUTH_TOKEN', 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX');

$factory = new SoapClientFactory(BLS_URL, array( // SoapClient options
    'cache_wsdl'         => WSDL_CACHE_NONE,
    'connection_timeout' => 30,
    'exceptions'         => true,
    'keep_alive'         => false,
    'uri'                => 'http://tempuri.org/',
    'soap_version'       => SOAP_1_2,
    'trace'              => true,
), array(                                        // Services
    'SalesLogix',
), array(                                        // Request preprocessors
    new WsaRequestPreprocessor(),
    new BlsAuthTokenRequestPreprocessor(BLS_AUTH_TOKEN),
));

$lead = new Contact(
        false, false, ContactGender::Female, false, ContactTitle::Dr,
        ContactType::Lead);
$lead->setFirstName('Marie');
$lead->setLastName('Curie');
$lead->setEmail('testlead@homelearningcollege.com');
$lead->setHomeTelephone('020 7946 0000');
$lead->setMobileTelephone('07700 900 000');
$lead->setReference(0);

$contacts = new ArrayOfContact();
$contacts->setContact(array($lead));

$courseInterests = new ArrayOfstring();
$courseInterests->setString(array('DNS1'));

$leadSource = new CreateLeadEntitiesLeadSource(
        CreateLeadEntitiesBrandAssociation::Floream, false,
        CreateLeadEntitiesSubmissionType::MiniRfi);
$leadSource->setCampaignCode('DTNDIR0002');

$request = new CreateLeadEntitiesCreateLeadRequest();
$request->setContacts($contacts);
$request->setCourseInterests($courseInterests);
$request->setLeadSource($leadSource);
$request = new CreateLead($request);

$client   = $factory->getInstance('SalesLogix');
$response = $client->CreateLead($request);
$result   = $response->getCreateLeadResult();

var_dump($result);
