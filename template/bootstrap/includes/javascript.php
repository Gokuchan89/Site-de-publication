<!-- JQUERY 3.1.1 -->
<script src="./template/bootstrap/js/jquery.min.js"></script>
<!-- BOOTSTRAP 3.3.7 -->
<script src="./template/bootstrap/js/bootstrap.min.js"></script>









<!-- Page derniers ajouts -->
<?php if ($op == "lastupdate") { ?>
	<!-- SLICK 1.6.0 -->
	<script src="./template/bootstrap/plugins/slick/js/slick.min.js"></script>
	<!-- LAZYLOAD 1.9.7 -->
	<script src="./template/bootstrap/plugins/lazyload/js/lazyload.min.js"></script>
	<script>
		document.title += " / <?php $category_name = new Category(); $category_name->getCategoryDBID($_GET['category']); echo $category_name->getName(); ?> / Derniers ajouts"
		
		// Slick
		$('.regular').slick(
		{
			infinite: true,
			lazyLoad: 'ondemand',
			autoplay: true,
			autoplaySpeed: 3000,
			slidesToShow: 6,
			slidesToScroll: 6,
			responsive: [
			{
				breakpoint: 1024,
				settings:
				{
					slidesToShow: 3,
					slidesToScroll: 3
				}
			},
			{
				breakpoint: 600,
				settings:
				{
					slidesToShow: 2,
					slidesToScroll: 2
				}
			},
			{
				breakpoint: 480,
				settings:
				{
					slidesToShow: 2,
					slidesToScroll: 2
				}
			}]
		});
		$('.embedded-gallery .slick-slide > img').each(function(){ 
			if ($(this).attr('slider_caption'))
			{
				var slideCaption = $(this).attr('slider_caption');
				$(this).parent('.slick-slide').append('<div class="slidecaption">' + slideCaption + '</div>');
			}
		});
				
		// LazyLoad
		$('img.lazy').lazyload(
		{
			effect : 'fadeIn'
		});
		
		// Popover
		$('[data-toggle="popover"]').popover(
		{
			html: true,
			trigger: "hover",
			placement: "auto right"
		});
	</script>
<?php } ?>








<!-- Page profil -->
<?php if ($op == "profile") { ?>
	<!-- BOOTSTRAP VALIDATOR 0.5.0 -->
	<script src="./template/bootstrap/plugins/bootstrap-validator/js/bootstrap-validator.min.js"></script>
	<script src="./template/bootstrap/plugins/bootstrap-validator/js/i18n/fr_FR.js"></script>
	<!-- CHOSEN 1.6.2 -->
	<script src="./template/bootstrap/plugins/chosen/js/chosen.min.js"></script>
	<!-- JASNY BOOTSTRAP 3.1.3 -->
	<script src="./template/bootstrap/plugins/jasny-bootstrap/js/jasny-bootstrap.min.js"></script>
	<!-- JQUERY UI 1.12.1 -->
	<script src="./template/bootstrap/plugins/jquery-ui/js/jquery-ui.min.js"></script>
	<script src="./template/bootstrap/plugins/jquery-ui/js/i18n/datepicker-fr.js"></script>
	<script>
		document.title += " / Profil"
		
		// Bootstrap validator
		$("#profileEditForm").bootstrapValidator(
		{
			locale: "fr_FR",
			fields:
			{
				profile_edit_email:
				{
					validators:
					{
						emailAdress:
						{
						},
						notEmpty:
						{
						}
					}
				},
				profile_edit_password1:
				{
					validators:
					{
						stringLength:
						{
							min: 6,
							max: 30
						},
						identical:
						{
							field: "profile_edit_password2"
						}
					}
				},
				profile_edit_password2:
				{
					validators:
					{
						identical:
						{
							field: "profile_edit_password1"
						}
					}
				},
				profile_edit_datebirthday:
				{
					validators:
					{
						date:
						{
							format: "DD/MM/YYYY"
						}
					}
				},
				profile_edit_website:
				{
					validators:
					{
						uri:
						{
						}
					}
				}
			}
		});
		
		// Datepicker
		$('.datepicker').datepicker(
		{
			firstDay: 1
		});

		// Chosen
		$(".chosen").chosen(
		{
			placeholder_text_single: "Choisir un pays"
		});
	</script>
<?php } ?>




<!-- Page thèmes -->
<?php if ($op == "themes") { ?>
	<script>
		document.title += " / Thèmes"
	</script>
<?php } ?>
		
		
		



<!-- Page paramètres -->
<?php if ($op == "settings") { ?>
	<!-- BOOTSTRAP VALIDATOR 0.5.0 -->
	<script src="./template/bootstrap/plugins/bootstrap-validator/js/bootstrap-validator.min.js"></script>
	<script src="./template/bootstrap/plugins/bootstrap-validator/js/i18n/fr_FR.js"></script>
	<!-- BOOTSTRAP WYSIHTML5 -->
	<script src="./template/bootstrap/plugins/bootstrap-wysihtml5/js/bootstrap3-wysihtml5.all.min.js"></script>
	<!-- CHOSEN 1.6.2 -->
	<script src="./template/bootstrap/plugins/chosen/js/chosen.min.js"></script>
	<script>
		document.title += " / Administration / Paramètres"
	
		var id_suppr;
		function category_del(id)
		{
			id_suppr = id;
			$("#ConfirmSupprCategory").modal();
		}
        function delCategory()
        {
			$.ajax({
				url: "./data/category_del.php",
				type: "POST",
				data:
				{
					id: id_suppr
				},
				success: function(response)
				{
					var result = $.trim(response);
					if (result == "success")
					{
						document.location.href = "./?op=settings&tab=2";
					}
				}
			})
        }
	
		var id_suppr;
		function menu_del(id)
		{
			id_suppr = id;
			$("#ConfirmSupprMenu").modal();
		}
        function delMenu()
        {
			$.ajax({
				url: "./data/menu_del.php",
				type: "POST",
				data:
				{
					id: id_suppr
				},
				success: function(response)
				{
					var result = $.trim(response);
					if (result == "success")
					{
						document.location.href = "./?op=settings&tab=2";
					}
				}
			})
        }

		// Bootstrap Validator
		$("#settingsForm").bootstrapValidator(
		{
			locale: "fr_FR",
			fields:
			{
				settings_title:
				{
					validators:
					{
						notEmpty:
						{
						}
					}
				},
				settings_avatar_width:
				{
					validators:
					{
						notEmpty:
						{
						},
						regexp:
						{
							regexp: /^[0-9]+$/
						}
					}
				},
				settings_avatar_height:
				{
					validators:
					{
						notEmpty:
						{
						},
						regexp:
						{
							regexp: /^[0-9]+$/
						}
					}
				},
				settings_avatar_weight:
				{
					validators:
					{
						notEmpty:
						{
						},
						regexp:
						{
							regexp: /^[0-9]+$/
						}
					}
				},
				settings_lastadd_max:
				{
					validators:
					{
						notEmpty:
						{
						},
						regexp:
						{
							regexp: /^[0-9]+$/
						}
					}
				}
			}
		});
		$("#categoryAddForm").bootstrapValidator(
		{
			locale: "fr_FR",
			fields:
			{
				category_add_name:
				{
					validators:
					{
						notEmpty:
						{
						},
						stringLength:
						{
							min: 4,
							max: 30
						}
					}
				}
			}
		});
		$("#categoryEditForm").bootstrapValidator(
		{
			locale: "fr_FR",
			fields:
			{
				category_edit_name:
				{
					validators:
					{
						notEmpty:
						{
						},
						stringLength:
						{
							min: 4,
							max: 30
						}
					}
				}
			}
		});
		$("#menuAddForm").bootstrapValidator(
		{
			locale: "fr_FR",
			fields:
			{
				menu_add_name:
				{
					validators:
					{
						notEmpty:
						{
						}
					}
				},
				menu_add_table:
				{
					validators:
					{
						notEmpty:
						{
						}
					}
				},
				menu_add_icon:
				{
					validators:
					{
						notEmpty:
						{
						}
					}
				}
			}
		});
		$("#menuEditForm").bootstrapValidator(
		{
			locale: "fr_FR",
			fields:
			{
				menu_edit_position:
				{
					validators:
					{
						notEmpty:
						{
						},
						regexp:
						{
							regexp: /^[0-9]+$/
						}
					}
				},
				menu_edit_name:
				{
					validators:
					{
						notEmpty:
						{
						}
					}
				},
				menu_edit_table:
				{
					validators:
					{
						notEmpty:
						{
						}
					}
				},
				menu_edit_icon:
				{
					validators:
					{
						notEmpty:
						{
						}
					}
				}
			}
		});
		
		// Bootstrap WYSIHTML5
		$(".textarea").wysihtml5(
		{
			toolbar:
			{
				"fa": true,
				"link": false,
				"image": false
			}
		});

		// Chosen
		$(".chosen").chosen(
		{
			disable_search: true,
			placeholder_text_single: "Choisir une catégorie"
		});
	</script>
<?php } ?>





<!-- Page utilisateurs -->
<?php if ($op == "users") { ?>
	<!-- BOOTSTRAP VALIDATOR 0.5.0 -->
	<script src="./template/bootstrap/plugins/bootstrap-validator/js/bootstrap-validator.min.js"></script>
	<script src="./template/bootstrap/plugins/bootstrap-validator/js/i18n/fr_FR.js"></script>
	<!-- CHOSEN 1.6.2 -->
	<script src="./template/bootstrap/plugins/chosen/js/chosen.min.js"></script>
	<!-- DATATABLES 1.10.12 -->
	<script src="./template/bootstrap/plugins/datatables/js/jquery.datatables.min.js"></script>
	<script src="./template/bootstrap/plugins/datatables/js/datatables.bootstrap.min.js"></script>
	<script>
		document.title += " / Administration / Utilisateurs"
		
        function user_edit_access(id, tab)
        {
            $.ajax({
                url: "./data/user_access.php",
                type: "POST",
                data:
				{
                    id: id,
					tab: tab
                },
                success: function(response)
				{
                    var result = $.trim(response);
                    if (result == "success")
					{
                       document.location.href = "./?op=users&tab="+tab;
                    }
                }
            })
        }
	
		var id_suppr;
		function user_del(id)
		{
			id_suppr = id;
			$("#ConfirmSupprUser").modal();
		}
        function delUser()
        {
			$.ajax({
				url: "./data/user_del.php",
				type: "POST",
				data:
				{
					id: id_suppr
				},
				success: function(response)
				{
					var result = $.trim(response);
					if (result == "success")
					{
						document.location.href = "./?op=users";
					}
				}
			})
        }
		
		// Bootstrap validator
		$("#userAddForm").bootstrapValidator(
		{
			locale: "fr_FR",
			fields:
			{
				user_add_username:
				{
					validators:
					{
						notEmpty:
						{
						},
						stringLength:
						{
							min: 4,
							max: 30
						},
						regexp:
						{
							regexp: /^[a-zA-Z0-9]+$/
						},
						remote:
						{
							message: "Cet identifiant est déjà utilisé",
							type: "POST",
							url: "./data/verif_username.php",
							data: function(validator)
							{
								return {
									user_add_username: validator.getFieldElements("user_add_username").val()
								};
							}
						}
					}
				},
				user_add_email:
				{
					validators:
					{
						emailAdress:
						{
						},
						notEmpty:
						{
						},
						remote:
						{
							message: "Cette adresse email est déjà utilisée",
							type: "POST",
							url: "data/verif_email.php",
							data: function(validator)
							{
								return {
									user_add_email: validator.getFieldElements("user_add_email").val()
								};
							}
						}
					}
				},
				user_add_password1:
				{
					validators:
					{
						notEmpty:
						{
						},
						stringLength:
						{
							min: 6,
							max: 30
						},
						identical:
						{
							field: "user_add_password2"
						}
					}
				},
				user_add_password2:
				{
					validators:
					{
						notEmpty:
						{
						},
						identical:
						{
							field: "user_add_password1"
						}
					}
				}
			}
		});
		
		// DataTables
		$("#users_list").DataTable(
		{
			'ordering': false,
			"pageLength": 10,
			"language":
			{
				"url": "./template/bootstrap/plugins/datatables/fr_FR.json"
			}
		});

		// Chosen
		$(".chosen").chosen(
		{
			disable_search: true
		});
	</script>
<?php } ?>





<!-- Page historique d'activité -->
<?php if ($op == "log") { ?>
	<!-- DATATABLES 1.10.12 -->
	<script src="./template/bootstrap/plugins/datatables/js/jquery.datatables.min.js"></script>
	<script src="./template/bootstrap/plugins/datatables/js/datatables.bootstrap.min.js"></script>
	<script>
		document.title += " / Administration / Historique d'activité"
		
		// DataTables
		$("#log_list").DataTable(
		{
			"order": [[ 0, 'desc' ]],
			"pageLength": 25,
			"language":
			{
				"url": "./template/bootstrap/plugins/datatables/fr_FR.json"
			},
			"initComplete": function ()
			{
				var api = this.api();
				api.$("td").click( function ()
				{
					api.search( this.innerHTML ).draw();
				});
			}
		});
	</script>
<?php } ?>