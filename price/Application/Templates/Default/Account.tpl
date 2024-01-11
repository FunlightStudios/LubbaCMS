{assign var=header value=$this->createView('Header.tpl', $pageId)}
{$this->displayView($header)}
{assign var=language value=$this->getLanguage()->json()}
			<main class="mdl-layout__content">
				<div class="mdl-layout__tab-panel is-active margin-from-header" id="overview">
					<section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--2dp">
						<div class="mdl-card mdl-cell mdl-cell--12-col">
							<div class="mdl-card__supporting-text">
								<h4>{$language['Titles']['Account']}</h4>

								{assign var=username value=$this->getUser()->getRow()->username|htmlspecialchars}
								<div>{$language['Account']['Index']['Welcome']|replace:'%username%':$username}</div>
							</div>
						</div>
					</section>
				</div>
			</main>
{assign var=footer value=$this->createView('Footer.tpl', $pageId)}
{$this->displayView($footer)}
