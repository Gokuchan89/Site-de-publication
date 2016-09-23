<?php
	// Protection des variables
	$_ = array_merge($_GET, $_POST);
	foreach ($_ as $key=>&$val)
	{
		Functions::secure($val);
	}
	
	/* Configure le script en français */
	setlocale(LC_TIME, 'fr_FR','fra');
	//Définit le décalage horaire par défaut de toutes les fonctions date/heure  
	date_default_timezone_set('Europe/Paris');
	
	if (!file_exists('./modules/commentaires/'.$detail['ID'].'.xml'))
	{
		$file = fopen('./modules/commentaires/'.$detail['ID'].'.xml', 'w+'); 
	
		$fichier = "<?xml version=\"1.0\" encoding=\"iso-8859-15\"?>\n";
		$fichier = $fichier."<commentaires>\n";
		$fichier = $fichier."</commentaires>";
		
		$monFichier = fopen('./modules/commentaires/'.$detail['ID'].'.xml', 'a+');
		fputs($monFichier, $fichier); 
		fclose($monFichier);
	}
	
	if (!file_exists('./modules/commentaires/'.$detail['ID'].'_config.xml'))
	{
		$file = fopen('./modules/commentaires/'.$detail['ID'].'_config.xml', 'w+'); 
	
		$fichier = "<?xml version=\"1.0\" encoding=\"iso-8859-15\"?>\n";
		$fichier = $fichier."<config>\n";
		$fichier = $fichier."<nbmessages>0</nbmessages>\n";
		$fichier = $fichier."<dernierid>0</dernierid>\n";
		$fichier = $fichier."</config>";
		
		$monFichier = fopen('./modules/commentaires/'.$detail['ID'].'_config.xml', 'a+');
		fputs($monFichier, $fichier); 
		fclose($monFichier);
	}
	
	if (isset($_['commentButton']))
	{
		if (!empty($_['comment_username']) && !empty($_['comment_email']) && !empty($_['comment_captcha']) && !empty($_['comment_comment']) && !empty($_['comment_rating']))
		{
			$username = $_['comment_username'];
			$email = $_['comment_email'];
			$comment = $_['comment_comment'];
			$rating = $_['comment_rating'];
			
			$captcha = $_['comment_captcha'];
			$verif = $_SESSION['aleat_nbr'];
			
			$ip = 'test';
			
			$ok = true;
			
			$bibliotheque = simplexml_load_file('./modules/commentaires/'.$id.'.xml');
			$config = simplexml_load_file('./modules/commentaires/'.$id.'_config.xml');
			
			foreach ($bibliotheque->commentaire as $commentaire)
			{
				if ($commentaire->auteur == $username && $commentaire->mail == $email && $commentaire->message == $comment && $commentaire->ip == $ip)
				{
					$ok = false;
				}
			}

			if ($ok)
			{
				// On va vérifier que les chiffres tapées dans "captcha" correspondent bien aux chiffres présents dans "verif"
				if ($captcha == $verif)
				{
					// Utilisation de la méthode addChild qui ajoute un noeud enfant
					$newMessage = $bibliotheque->addChild('commentaire');

					// On met d'abord à jour le nombre de messages
					$nb = $config->nbmessages;
					settype($nb, 'integer');
					$nb += 1;
					$config->nbmessages = $nb;

					// Ensuite on met à jour l'id
					$dernierid = $config->dernierid;
					settype($dernierid, 'integer');
					$dernierid += 1;
					$config->dernierid = $dernierid;

					$newMessage->addAttribute('identifiant', $dernierid);

					// Ensuite le reste
					$newMessage->addchild('auteur', $username);
					$newMessage->addchild('mail', $email);
					$newMessage->addchild('date', date('d.m.y'));
					$newMessage->addchild('heure', date('H:i:s'));
					$newMessage->addchild('ip', $ip);
					$newMessage->addchild('message', $comment);
					$newMessage->addchild('note', $rating);

					// On crée la nouvelle chaine XML pour la bibliothèque
					$baseMiseAJour = $bibliotheque->asXML() ;
					// et pour la config
					$configMiseAJour = $config->asXML() ;

					// On écrit la config
					$fichierconf = fopen('./modules/commentaires/'.$id.'_config.xml', 'w');
					fputs($fichierconf, $configMiseAJour);
					// et le nouveau message
					$fichier = fopen('./modules/commentaires/'.$id.'.xml', 'w');
					fputs($fichier, $baseMiseAJour);
					
					header('location: '.$_SERVER['REQUEST_URI']);
				} else {
					$message = '<div class="alert alert-danger">Le code de s&eacute;curit&eacute; que vous avez saisi est incorrect.</div>';
				}
			} else {
				$message = '<div class="alert alert-danger">Votre message a d&eacute;j&#224; &eacute;t&eacute; ajout&eacute;.<br />Merci de votre commentaire.</div>';
			}
		} else {
			$message = '<div class="alert alert-danger">Une erreur s\'est produite, veuillez nous en excuser.</div>';
		}
	}
?>
<style>
.rating {
    float:left;
}

.rating:not(:checked) > input
{
	position: absolute;
	width: 1px;
	height: 1px;
	padding: 0;
	margin: -1px;
	overflow: hidden;
    clip: rect(0px, 0px, 0px, 0px);
}

.rating:not(:checked) > label
{
    float:right;
    width:1em;
    padding:0 .1em;
    overflow:hidden;
    white-space:nowrap;
    cursor:pointer;
    font-size:200%;
    line-height:1.2;
    color:#ddd;
    text-shadow:1px 1px #bbb, 2px 2px #666, .1em .1em .2em rgba(0,0,0,.5);
}

.rating:not(:checked) > label:before
{
    content: '\2605  ';
}

.rating > input:checked ~ label
{
    color: #f70;
    text-shadow:1px 1px #c60, 2px 2px #940, .1em .1em .2em rgba(0,0,0,.5);
}

.rating:not(:checked) > label:hover,
.rating:not(:checked) > label:hover ~ label
{
    color: gold;
    text-shadow:1px 1px goldenrod, 2px 2px #B57340, .1em .1em .2em rgba(0,0,0,.5);
}

.rating > input:checked + label:hover,
.rating > input:checked + label:hover ~ label,
.rating > input:checked ~ label:hover,
.rating > input:checked ~ label:hover ~ label,
.rating > label:hover ~ input:checked ~ label
{
    color: #ea0;
    text-shadow:1px 1px goldenrod, 2px 2px #B57340, .1em .1em .2em rgba(0,0,0,.5);
}

.rating > label:active
{
    position:relative;
    top:2px;
    left:2px;
}

.comments .media-heading
{
    margin-top: 25px;
}

.comments .comment-info
{
    margin-left: 6px;
    margin-top: 21px;
}

.comments .comment-info .btn
{
    font-size: 0.8em;
}

.comments .comment-info .fa
{
    line-height: 10px;
}

.comments .media-body p
{
    position: relative;
    background: #F7F7F7;
    padding: 15px;
    margin-top: 50px;
}

.comments .media-body p::before
{
    background-color: #F7F7F7;
    box-shadow: -2px 2px 2px 0 rgba( 178, 178, 178, .4 );
    content: "\00a0";
    display: block;
    height: 30px;
    left: 20px;
    position: absolute;
    top: -13px;
    transform: rotate( 135deg );
    width:  30px;
}

.white
{
    color: #fff;
}
</style>
<div class="col-xs-12 col-sm-12 col-md-8">
	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-comments"></i> Commentaires</h3></div>
		<div class="panel-body">
			<?php
				$bibliotheque = simplexml_load_file('./modules/commentaires/'.$id.'.xml');
				$config = simplexml_load_file('./modules/commentaires/'.$id.'_config.xml');
			
				$nbmessages = $config->nbmessages;
				settype($nbmessages, 'integer');
				if ($nbmessages == 0)
				{
					echo '<div class="alert alert-info text-center">Aucun commentaire &#224; afficher</div>';
				} else {
					$somme_notes = 0;
					$i = 1;
					foreach ($bibliotheque->commentaire as $commentaire)
					{
						if ($i++ > 0)
						{
							$somme_notes += $commentaire->note;
							
							$total_query = $db->prepare('SELECT COUNT(`id`) FROM `site_user` WHERE `username` = :username');
							$total_query->bindValue('username', $commentaire->auteur, PDO::PARAM_STR);
							$total_query->execute();
							$total = $total_query->fetchColumn();
							$total_query->CloseCursor();
							
							if ($total == 1)
							{
								$query = $db->prepare('SELECT `avatar` FROM `site_user` WHERE `username` = :username');
								$query->bindValue('username', $commentaire->auteur, PDO::PARAM_STR);
								$query->execute();
								$user = $query->fetch();
								$query->CloseCursor();
								
								$avatar = './img/avatar/'.$user['avatar'];
							} else {
								$avatar = './img/avatar/1.png';
							}
							
							echo '
								<div class="row">
									<div class="col-lg-12 col-sm-12 col-xs-12">
										<ul class="media-list comments">
											<li class="media">
												<div class="pull-left"><img src="'.$avatar.'" class="media-object img-circle img-thumbnail" width="64" alt="Generic placeholder image"></div>
												<div class="media-body">
													<h5 class="media-heading pull-left">'.$commentaire->auteur.'</h5>
													<div class="comment-info pull-left">
														<div class="btn btn-default btn-xs"><i class="fa fa-clock-o"></i> le '.$commentaire->date.' à '.$commentaire->heure.'</div>
													</div>
													<br class="clearfix">
													<div class="pull-right"><img src="./img/stars/'.($commentaire->note*2).'.png"></img></div>
													<p class="well"><br/>'.$commentaire->message.'</p>
												</div>
											</li>
										</ul>
									</div>
								</div>
							';
						}
					}
				}
			?>
		</div>
	</div>
</div>
<div class="col-xs-12 col-sm-12 col-md-4">
	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-comments"></i> Poster un commentaire</h3></div>
		<form method="post" id="commentForm">
			<div class="panel-body">
				<?php if (isset($message)) echo $message; ?>
				<div class="form-group has-feedback">
					<input type="text" class="form-control" name="comment_username" value="<?php if (isset($_SESSION['username'])) echo $_SESSION['username']; ?>" placeholder="Identifiant" required />
					<span class="form-control-feedback"><i class="fa fa-user"></i></span>
				</div>
				<div class="form-group has-feedback">
					<input type="email" class="form-control" name="comment_email" placeholder="Email" required />
					<span class="form-control-feedback"><i class="fa fa-envelope"></i></span>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-xs-9 col-sm-8 col-md-8">
							<div class="form-group has-feedback">
								<input type="text" class="form-control" name="comment_captcha" placeholder="Code de sécurité" required />
								<span class="form-control-feedback"><i class="fa fa-key"></i></span>
							</div>
						</div>
						<div class="col-xs-3 col-sm-4 col-md-4">
							<img src="../includes/captcha.php" class="pull-right" title="Code de vérification" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<textarea class="form-control" name="comment_comment" rows="3" placeholder="Votre commentaire" required></textarea>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-xs-6 col-sm-6 col-md-6" style="margin-top:10px;">
							<label>Notez le film</label>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6">
							<fieldset class="rating pull-right">
								<input type="radio" id="star5" name="comment_rating" value="5" required /><label for="star5" title="">5 stars</label>
								<input type="radio" id="star4" name="comment_rating" value="4" required /><label for="star4" title="">4 stars</label>
								<input type="radio" id="star3" name="comment_rating" value="3" required /><label for="star3" title="">3 stars</label>
								<input type="radio" id="star2" name="comment_rating" value="2" required /><label for="star2" title="">2 stars</label>
								<input type="radio" id="star1" name="comment_rating" value="1" required /><label for="star1" title="">1 star</label>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
			<div class="panel-footer clearfix">
				<div class="pull-right">
					<button type="submit" class="btn btn-success btn-block btn-flat" name="commentButton">Envoyer</button>
				</div>
			</div>
		</form>
	</div>
</div>