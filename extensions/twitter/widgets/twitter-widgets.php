<?php
/**
 * Twitter Widget
 * Wordpress 2.8 and above
 * @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
 */
class socmeTwitterWidgets extends WP_Widget {
	
    /**
     * Constructor
     *
     * @return void
     **/
	function socmeTwitterWidgets() {
		$widget_ops = array( 'classname' => 'Twitter Widgets', 'description' => 'This widget allows you to display any of the 4 supported twitter widgets in your sidebar.' );
		$this->WP_Widget( 'Twitter Widgets', 'Twitter Widgets', $widget_ops );
		add_action( 'admin', '' );
		add_action( 'admin_print_scripts-widgets.php', array( &$this, 'farbtastic' ), 1 );
	}

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme 
     * @param array  An array of settings for this widget instance 
     * @return void Echoes it's output
     **/
	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );

$profile = <<<END
<script>
	new TWTR.Widget({
		version: 2,
		type: 'profile',
		rpp: 4,
		interval: 6000,
		width: 250,
		height: 300,
		theme: {
			shell: {
				background: '#333333',
				color: '#ffffff'
			},
			tweets: {
				background: '#000000',
				color: '#ffffff',
				links: '#4aed05'
			}
		},
		features: {
			scrollbar: false,
			loop: false,
			live: false,
			hashtags: true,
			timestamp: true,
			avatars: false,
			behavior: 'all'
		}
	}).render().setUser('brandondove').start();
</script>
END;
$search = <<<END
<script src="http://widgets.twimg.com/j/2/widget.js"></script>
<script>
new TWTR.Widget({
  version: 2,
  type: 'search',
  search: 'omg',
  interval: 6000,
  title: 'Excitement is in the air...',
  subject: 'OMG!',
  width: 'auto',
  height: 300,
  theme: {
    shell: {
      background: '#8ec1da',
      color: '#ffffff'
    },
    tweets: {
      background: '#ffffff',
      color: '#444444',
      links: '#1985b5'
    }
  },
  features: {
    scrollbar: true,
    loop: true,
    live: true,
    hashtags: true,
    timestamp: true,
    avatars: true,
    toptweets: true,
    behavior: 'default'
  }
}).render().start();
</script>
END;
	}

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings 
     * @return array The validated and (if necessary) amended settings
     **/
	function update( $new_instance, $old_instance ) {
		// update logic goes here
		$updated_instance = $new_instance;
		return $updated_instance;
	}

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     **/
	function form( $instance ) {
		extract( get_option( 'socme-twitter-credentials') );
		$defaults = array(
			'type' =>			'profile', // profile, search, faves, list
			
			// profile, faves
			'screen_name' =>	$screen_name,
			
			// search, faves
			'search_string' =>	$screen_name,
			'title' =>			'',
			'caption' =>		'',
			
			// search only
			'top_tweets' =>	'1',
			
			// list only
			'list' =>		'',
			
			// ALL
			'loop_results' =>	'1',
			'interval' =>		'6',
			'poll' =>			'1',
			'scrollbar' =>		'1',
			'behavior' =>		'timed', // timed, load
			'num_tweets' =>		'4',
			'avatars' =>		'1',
			'timestamps' =>		'1',
			'hashtags' =>		'1',
			'shell_bg' =>		'#cccccc',
			'shell_text' =>		'#000000',
			'tweet_bg' =>		'#eeeeee',
			'tweet_text' =>		'#000000',
			'links' =>			'#0000ff',
			'width' =>			'250',
			'height' =>			'300',
			'autowidth' =>		'1'
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		// pring_r( $instance );
		?>
		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Widget Type:'); ?>
				<select id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
					<option value="profile"<?php echo ( $instance['type'] == 'profile' ) ? 'selected="selected"' : ''; ?>>Profile</option>
					<option value="search"<?php echo ( $instance['type'] == 'search' ) ? 'selected="selected"' : ''; ?>>Search</option>
					<option value="faves"<?php echo ( $instance['type'] == 'faves' ) ? 'selected="selected"' : ''; ?>>Favorites</option>
					<option value="list"<?php echo ( $instance['type'] == 'list' ) ? 'selected="selected"' : ''; ?>>List</option>
				</select>
			</label>
		</p>
		<!-- Screen Name -->
		<p>
			<label for="<?php echo $this->get_field_id('screen_name'); ?>"><?php _e('Screen Name:'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('screen_name'); ?>" name="<?php echo $this->get_field_name('screen_name'); ?>" type="text" value="<?php echo $instance['screen_name']; ?>" />
			</label>
		</p>
		<!-- Search String/Title/Caption -->
		<p>
			<label for="<?php echo $this->get_field_id('search_string'); ?>"><?php _e('Search String:'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('search_string'); ?>" name="<?php echo $this->get_field_name('search_string'); ?>" type="text" value="<?php echo $instance['search_string']; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('caption'); ?>"><?php _e('Caption:'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('caption'); ?>" name="<?php echo $this->get_field_name('caption'); ?>" type="text" value="<?php echo $instance['caption']; ?>" />
			</label>
		</p>
		<!-- List Name -->
		<p>
			<label for="<?php echo $this->get_field_id('list'); ?>"><?php _e('List:'); ?>
				<select id="<?php echo $this->get_field_id('list'); ?>" name="<?php echo $this->get_field_name('list'); ?>">
					<option value="profile"<?php echo ( $instance['list'] == 'profile' ) ? 'selected="selected"' : ''; ?>>Profile</option>
				</select>
			</label>
		</p>
		<!-- Top Tweets -->
		<p>
			<label for="<?php echo $this->get_field_id('top_tweets'); ?>">
				<input id="<?php echo $this->get_field_id('top_tweets'); ?>" name="<?php echo $this->get_field_name('top_tweets'); ?>" type="checkbox" value="1"<?php echo ( $instance['top_tweets'] == '1' ) ? ' checked="checked"' : ''; ?> />
				<?php _e('Show Top Tweets?:'); ?>
			</label>
		</p>
		<hr />
		<!-- Loop Tweets -->
		<p>
			<label for="<?php echo $this->get_field_id('loop_results'); ?>">
				<input id="<?php echo $this->get_field_id('loop_results'); ?>" name="<?php echo $this->get_field_name('loop_results'); ?>" type="checkbox" value="1"<?php echo ( $instance['loop_results'] == '1' ) ? ' checked="checked"' : ''; ?> />
				<?php _e('Loop Results?:'); ?>
			</label>
		</p>
		<!-- Poll -->
		<p>
			<label for="<?php echo $this->get_field_id('poll'); ?>">
				<input id="<?php echo $this->get_field_id('poll'); ?>" name="<?php echo $this->get_field_name('poll'); ?>" type="checkbox" value="1"<?php echo ( $instance['poll'] == '1' ) ? ' checked="checked"' : ''; ?> />
				<?php _e('Poll for new results?:'); ?>
			</label>
		</p>
		<!-- Scrollbar -->
		<p>
			<label for="<?php echo $this->get_field_id('scrollbar'); ?>">
				<input id="<?php echo $this->get_field_id('scrollbar'); ?>" name="<?php echo $this->get_field_name('scrollbar'); ?>" type="checkbox" value="1"<?php echo ( $instance['scrollbar'] == '1' ) ? ' checked="checked"' : ''; ?> />
				<?php _e('Include scrollbar?:'); ?>
			</label>
		</p>
		<!-- Avatars -->
		<p>
			<label for="<?php echo $this->get_field_id('avatars'); ?>">
				<input id="<?php echo $this->get_field_id('avatars'); ?>" name="<?php echo $this->get_field_name('avatars'); ?>" type="checkbox" value="1"<?php echo ( $instance['avatars'] == '1' ) ? ' checked="checked"' : ''; ?> />
				<?php _e('Include avatars?:'); ?>
			</label>
		</p>
		<!-- Timestamps -->
		<p>
			<label for="<?php echo $this->get_field_id('timestamps'); ?>">
				<input id="<?php echo $this->get_field_id('timestamps'); ?>" name="<?php echo $this->get_field_name('timestamps'); ?>" type="checkbox" value="1"<?php echo ( $instance['timestamps'] == '1' ) ? ' checked="checked"' : ''; ?> />
				<?php _e('Include time stamps?:'); ?>
			</label>
		</p>
		<!-- Hashtags -->
		<p>
			<label for="<?php echo $this->get_field_id('hastags'); ?>">
				<input id="<?php echo $this->get_field_id('hashtags'); ?>" name="<?php echo $this->get_field_name('hashtags'); ?>" type="checkbox" value="1"<?php echo ( $instance['hashtags'] == '1' ) ? ' checked="checked"' : ''; ?> />
				<?php _e('Include hash tags?:'); ?>
			</label>
		</p>
		<!-- Behavior -->
		<p>Behavior<br />
			<label for="<?php echo $this->get_field_id('timed'); ?>">
				<input id="<?php echo $this->get_field_id('timed'); ?>" name="<?php echo $this->get_field_name('behavior'); ?>" type="radio" value="timed"<?php echo ( $instance['behavior'] == 'timed' ) ? ' checked="checked"' : ''; ?> />
				<?php _e('Timed interval'); ?>
			</label>
			<label for="<?php echo $this->get_field_id('load'); ?>">
				<input id="<?php echo $this->get_field_id('load'); ?>" name="<?php echo $this->get_field_name('behavior'); ?>" type="radio" value="load"<?php echo ( $instance['behavior'] == 'load' ) ? ' checked="checked"' : ''; ?> />
				<?php _e('Load all Tweets'); ?>
			</label>
		</p>
		
		<?php
			$this->add_radio_group( __( 'Behavior', SOCME ), array( __( 'Timed Interval', SOCME ), __( 'Load all tweets', SOCME ) ), 'behavior', array( 'timed', 'load' ), $instance['behavior'] );
			$this->add_text_field( __( 'Number of Tweets', SOCME ), 'num_tweets', $instance['num_tweets'] );
		?>
<?php
	}
	
	function add_text_field( $field_label='', $field_name='', $field_value='' ) {
		$field = '
		<!-- '.$field_label.' -->
		<p>
			<label for="'.$this->get_field_id( $field_name ).'">'.$field_label.'
				<input class="widefat" id="'.$this->get_field_id( $field_name ).'" name="'.$this->get_field_name( $field_name ).'" type="text" value="'.$field_value.'" />
			</label>
		</p>';
		echo $field;
	}
	function add_radio_group( $group_label='', $field_labels=array(), $field_name='', $field_values=array(), $selected='' ) {
		$group = '
		<!-- '.$group_label.' -->
		<p>'.$group_label.'<br />';
		for( $i = 0; $i < count( $field_labels ); $i++ ) :
			$group.= '
				<label for="'.$this->get_field_id( $field_values[$i] ).'">
					<input class="widefat" id="'.$this->get_field_id( $field_values[$i] ).'" name="'.$this->get_field_name( $field_name ).'" type="radio" value="'.$field_values[$i].'"'.( ( $selected == $field_values[$i] ) ? ' checked="checked"' : '').' />
					'.$field_labels[$i].'
				</label>';
		endfor;
		$group.='
		</p>';
		echo $group;
	}
	
	function farbtastic() {
		wp_enqueue_script( 'farbtastic' );
	}
}

add_action( 'widgets_init', create_function( '', "register_widget('socmeTwitterWidgets');" ) );