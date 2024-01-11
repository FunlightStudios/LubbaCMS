{assign var=language value=$this->getLanguage()->json()}
			<section class="section--center mdl-grid mdl-grid--no-spacing">
				<div class="mdl-cell mdl-cell--12-col">
					<div style="height:50px">{$language['Copyright']|replace:'%sitename%':$settings->getSiteName()|replace:'%url%':$settings->getSitePath()}</div>
				</div>
			</section>
		</div>
	</body>
</html>
