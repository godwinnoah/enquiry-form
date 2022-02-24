<?php
namespace Akaninyene\Upworkone;

use Rakit\Validation\Validator;
use Akaninyene\Upworkone\SQLiteConnection;
use Ausi\SlugGenerator\SlugGenerator;
use Ramsey\Uuid\Uuid;

class Enquiry
{
    private $table = 'enquiries';

    private $validator;
    
    private $pdo;

    function __construct()
    {
        $this->validator = new Validator();

        // Connect to the database
        $conn = new SQLiteConnection();
        $this->pdo = $conn->connect();

        // Setup Create Table if it does not exist
        $this->createEnquiryTableIfNotExists();
    }


    public function save(Array $data): array
    {
        $validationRules = [
            'firstName' => 'required|alpha|min:2|max:32',
            'lastName'  => 'required|alpha|min:2|max:32',
            'email'     => 'required|email',
            'subject'   => 'required|min:12|max:200',
            'message'   => 'required|min:20|max:1000',
        ];

        $errors = $this->validate($data, $validationRules);
        if (is_array($errors)) {
            return ['errors' => $errors];
        }


        // Add some meta data to the submitted enquiry
        $data['uuid'] = (Uuid::uuid4())->toString();
        $data['slug'] = $this->generateTimestampedSlug($data['subject']);
        $data['created'] = date("Y-m-d H:i:s");


        // Data will be persisted to database
        $query = sprintf("INSERT INTO %s (uuid, first_name, last_name, email, subject, message, slug, created) VALUES 
                (:uuid, :firstName, :lastName, :email, :subject, :message, :slug, :created)", $this->table);
        $stmt = $this->pdo->prepare($query, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        $stmt->execute($data);


        // Confirm to user that the data is saved
        return ['status' => 'saved'];
    }





    public function load(): array
    {
        $contacts = [];
        // Fetch the saved contact
        $stmt = $this->pdo->prepare(sprintf("SELECT * FROM %s", $this->table));
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                array_push($contacts, $row);
            }
        }
        
        return $contacts;
    }



    public function loadAnEnquiryBySlug($slug)
    {
        $query = sprintf("SELECT * FROM %s WHERE `slug`=?", $this->table);
        $stmt = $this->pdo->prepare($query);
        $enquiry = [];
        if ($stmt->execute([$slug])) {
            $enquiry = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $enquiry;
    }



    public function loadAnEnquiryByUuid($uuid)
    {
        $query = sprintf("SELECT * FROM %s WHERE `uuid`=?", $this->table);
        $stmt = $this->pdo->prepare($query);
        $record = [];
        if ($stmt->execute([$uuid])) {
            $record = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $record;
    }

    

    private function validate($data, $rules): bool | array
    {

        $validation = $this->validator->make($data, $rules);
        $validation->validate();

        if ($validation->fails()) {
            $errors = $validation->errors();
            return $errors->firstOfAll();
        }

        return true;
    }


    private function createEnquiryTableIfNotExists()
    {
        $query = sprintf("CREATE TABLE IF NOT EXISTS `%s` (
            `uuid` CHAR(32) NOT NULL,
            `first_name` VARCHAR(32) NOT NULL,
            `last_name` VARCHAR(32) NOT NULL,
            `email` VARCHAR(225) NOT NULL,
            `subject` VARCHAR(200) NOT NULL,
            `message` TEXT,
            `slug` VARCHAR(255) NOT NULL,
            `created` DATETIME NOT NULL
        )", $this->table);
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
    }


    private function generateTimestampedSlug($sentence): string
    {
        $slugGenerator = new SlugGenerator();
        return sprintf("%s-%s", $slugGenerator->generate($sentence, ['validChars' => 'A-Za-z0-9']), time());
    }
}