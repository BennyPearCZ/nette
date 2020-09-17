<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function renderDefault(): void
{
	$this->template->posts = $this->database->table('posts')
		->order('created_at DESC')
		->limit(5);
}
protected function createComponentRegistrationForm(): Form
	{
		$form = new Form;
		$form->addText('name', 'Jméno:');
		$form->addEmail('email', 'Email:');
		$form->addTextArea('mes', 'Zpráva:');
                $form->addUpload('upload','Přípona:');
                $form->addSubmit('send', 'Odeslat');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
                
	}

	public function formSucceeded(Form $form, $data): void
	{
            
               $soubor = $data->upload;
               $soubor->move("uploads/" . $data->upload->name);
            
		$this->database->table('posts')->insert([
		'name' => $data->name,
		'email' => $data->email,
		'content' => $data->mes,
	]);
                
        $mail = new Nette\Mail\Message;
        $mail->setFrom('BennyPear@seznam.cz')
	->addTo('benny.lpik@gmail.com')
	->setSubject('Odpověd uživatele')
	->setBody("Odpověd uživatele {$data->name} \nJméno:{$data->name} \nEmail:{$data->email} \nZpráva: {$data->mes}")
        ->addAttachment('uploads/'. $data->upload->name);
        
        
        $mailer = new Nette\Mail\SmtpMailer([
	'host' => 'smtp.seznam.cz',
	'username' => 'BennyPear@seznam.cz',
	'password' => 'Hrusticka9585',
	'secure' => 'ssl',
]);
        $mailer->send($mail);
        $this->flashMessage('Odpověd odeslána!', 'success');
        
        
        
	}
        


}
