<?php if (!defined('NEOFRAG_CMS')) exit;
/**************************************************************************
Copyright © 2015 Michaël BILCOT & Jérémy VALENTIN

This file is part of NeoFrag.

NeoFrag is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

NeoFrag is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with NeoFrag. If not, see <http://www.gnu.org/licenses/>.
**************************************************************************/

class m_news_c_admin extends Controller_Module
{
	public function index($news)
	{
		$this	->title('Actualités')
				->load->library('table');
			
		$news = $this	->table
						->add_columns(array(
							array(
								'content' => '<?php echo ($data[\'published\']) ? \'<i class="fa fa-circle" data-toggle="tooltip" title="Publiée" style="color: #7bbb17;"></i>\' : \'<i class="fa fa-circle-o" data-toggle="tooltip" title="En attente de publication" style="color: #535353;"></i>\'; ?>',
								'sort'    => '{published}',
								'size'    => TRUE
							),
							array(
								'title'   => 'Titre',
								'content' => '<a href="{base_url}news/{news_id}/{url_title(title)}.html">{title}</a>',
								'sort'    => '{title}',
								'search'  => '{title}'
							),
							array(
								'title'   => 'Catégorie',
								'content' => '<a href="{base_url}admin/news/categories/{category_id}/{url_title(category_name)}.html"><img src="{image {category_icon}}" alt="" /> {category_title}</a>',
								'sort'    => '{category_title}',
								'search'  => '{category_title}'
							),
							array(
								'title'   => 'Auteur',
								'content' => '<?php echo $this->user->link($data[\'user_id\'], $data[\'username\']); ?>',
								'sort'    => '{username}',
								'search'  => '{username}'
							),
							array(
								'title'   => 'Date',
								'content' => '<span data-toggle="tooltip" title="<?php echo timetostr($NeoFrag->lang(\'date_time_long\'), $data[\'date\']); ?>">{time_span(date)}</span>',
								'sort'    => '{date}'
							),
							array(
								'title'   => '<i class="fa fa-comments-o" data-toggle="tooltip" title="Commentaires"></i>',
								'content' => '<?php echo $NeoFrag->load->library(\'comments\')->admin_comments(\'news\', $data[\'news_id\']); ?>',
								'size'    => TRUE
							),
							array(
								'content' => array(
									button_edit('{base_url}admin/news/{news_id}/{url_title(title)}.html'),
									button_delete('{base_url}admin/news/delete/{news_id}/{url_title(title)}.html')
								),
								'size'    => TRUE
							)
						))
						->sort_by(5, SORT_DESC, SORT_NUMERIC)
						->data($news)
						->no_data('Il n\'y a pas encore d\'actualité')
						->display();
			
		$categories = $this	->table
							->add_columns(array(
								array(
									'content' => '<a href="{base_url}admin/news/categories/{category_id}/{name}.html"><img src="{image {icon_id}}" alt="" /> {title}</a>',
									'search'  => '{title}',
									'sort'    => '{title}'
								),
								array(
									'content' => array(
										button_edit('{base_url}admin/news/categories/{category_id}/{name}.html'),
										button_delete('{base_url}admin/news/categories/delete/{category_id}/{name}.html')
									),
									'size'    => TRUE
								)
							))
							->pagination(FALSE)
							->data($this->model('categories')->get_categories())
							->no_data('Aucune catégorie')
							->display();

		return new Row(
			new Col(
				new Panel(array(
					'title'   => 'Catégories',
					'icon'    => 'fa-align-left',
					'content' => $categories,
					'footer'  => '<a class="btn btn-outline btn-success" href="{base_url}admin/news/categories/add.html"><i class="fa fa-plus"></i> Créer une catégorie</a>',
					'size'    => 'col-md-12 col-lg-3'
				))
			),
			new Col(
				new Panel(array(
					'title'   => 'Liste des actualités',
					'icon'    => 'fa-file-text-o',
					'content' => $news,
					'footer'  => '<a class="btn btn-outline btn-success" href="{base_url}admin/news/add.html"><i class="fa fa-plus"></i> Ajouter une actualité</a>',
					'size'    => 'col-md-12 col-lg-9'
				))
			)
		);
	}
	
	public function add()
	{
		$this	->subtitle('Ajouter une actualité')
				->load->library('form')
				->add_rules('news', array(
					'categories' => $this->model('categories')->get_categories_list(),
				))
				->add_submit('Ajouter')
				->add_back('admin/news.html');

		if ($this->form->is_valid($post))
		{
			$this->model()->add_news(	$post['title'],
										$post['category'],
										$post['image'],
										$post['introduction'],
										$post['content'],
										$post['tags'],
										in_array('on', $post['published']));

			add_alert('Succes', 'News ajoutée');

			redirect_back('admin/news.html');
		}

		return new Panel(array(
			'title'   => 'Ajouter une actualité',
			'icon'    => 'fa-file-text-o',
			'content' => $this->form->display()
		));
	}

	public function _edit($news_id, $category_id, $user_id, $image_id, $date, $published, $views, $vote, $title, $introduction, $content, $tags, $category_name, $category_title, $news_image, $category_image, $category_icon)
	{
		$this	->title('&Eacute;dition')
				->subtitle($title)
				->load->library('form')
				->add_rules('news', array(
					'title'        => $title,
					'category_id'  => $category_id,
					'categories'   => $this->model('categories')->get_categories_list(),
					'image_id'     => $image_id,
					'introduction' => $introduction,
					'content'      => $content,
					'tags'         => $tags,
					'published'    => $published
				))
				->add_submit('Éditer')
				->add_back('admin/news.html');

		if ($this->form->is_valid($post))
		{
			$this->model()->edit_news(	$news_id,
										$post['category'],
										$post['image'],
										in_array('on', $post['published']),
										$post['title'],
										$post['introduction'],
										$post['content'],
										$post['tags'],
										$this->config->lang);

			add_alert('Succes', 'News éditée');

			redirect_back('admin/news.html');
		}

		return new Panel(array(
			'title'   => 'Éditer l\'actualité',
			'icon'    => 'fa-align-left',
			'content' => $this->form->display()
		));
	}

	public function delete($news_id, $title)
	{
		$this	->title('Suppression actualité')
				->subtitle($title)
				->load->library('form')
				->confirm_deletion('Confirmation de suppression', 'Êtes-vous sûr(e) de vouloir supprimer l\'actualité <b>'.$title.'</b> ?<br />Tous les commentaires associés à cette actualité seront aussi supprimés.');

		if ($this->form->is_valid())
		{
			$this->model()->delete_news($news_id);

			return 'OK';
		}

		echo $this->form->display();
	}
	
	public function _categories_add()
	{
		$this	->subtitle('Ajouter une catégorie')
				->load->library('form')
				->add_rules('categories')
				->add_back('admin/news.html')
				->add_submit('Ajouter');

		if ($this->form->is_valid($post))
		{
			$this->model('categories')->add_category(	$post['title'],
														$post['image'],
														$post['icon']);

			add_alert('Succes', 'Catégorie ajoutée avec succès');

			redirect_back('admin/news.html');
		}
		
		return new Panel(array(
			'title'   => 'Ajouter une catégorie',
			'icon'    => 'fa-align-left',
			'content' => $this->form->display()
		));
	}
	
	public function _categories_edit($category_id, $title, $image_id, $icon_id)
	{
		$this	->subtitle('Catégorie '.$title)
				->load->library('form')
				->add_rules('categories', array(
					'title' => $title,
					'image' => $image_id,
					'icon'  => $icon_id
				))
				->add_submit('Éditer')
				->add_back('admin/news.html');
		
		if ($this->form->is_valid($post))
		{
			$this->model('categories')->edit_category(	$category_id,
														$post['title'],
														$post['image'],
														$post['icon']);
		
			add_alert('Succes', 'Catégorie éditée avec succès');

			redirect_back('admin/news.html');
		}
		
		return new Panel(array(
			'title'   => 'Éditer la catégorie',
			'icon'    => 'fa-align-left',
			'content' => $this->form->display()
		));
	}
	
	public function _categories_delete($category_id, $title)
	{
		$this	->title('Suppression catégorie')
				->subtitle($title)
				->load->library('form')
				->confirm_deletion('Confirmation de suppression', 'Êtes-vous sûr(e) de vouloir supprimer la catégorie <b>'.$title.'</b> ?<br />Toutes les actualités associées à cette catégorie seront aussi supprimées.');
				
		if ($this->form->is_valid())
		{
			$this->model('categories')->delete_category($category_id);

			return 'OK';
		}

		echo $this->form->display();
	}
}

/*
NeoFrag Alpha 0.1
./modules/news/controllers/admin.php
*/