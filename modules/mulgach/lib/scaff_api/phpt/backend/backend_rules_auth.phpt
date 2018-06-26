'action_access'=>array(
						new \ActionAccessRule('deny',_array_diff($this->getActions(),array({allowed_methods})),'anonym','{auth_con}/login')
				),