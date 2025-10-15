<footer class="modern-footer">
	<div class="center">
		<div class="footer-content">
			<div class="footer-left">
				<strong><?= htmlspecialchars($config['hotelName']) ?> Hotel</strong> &copy; 2011 - <?= date('Y') ?>
				<br>
				<small>Started by Randy & Flimmerherzi, owned by Jerry</small>
			</div>
			<div class="footer-right">
				<div class="footer-badge">
					<i class="fas fa-code"></i> Lubba CMS v<?= htmlspecialchars($config['lubbaversion'] ?? '2.0') ?>
				</div>
				<div class="footer-badge">
					<i class="fab fa-php"></i> PHP <?= PHP_VERSION ?>
				</div>
			</div>
		</div>
	</div>
</footer>

<style>
.modern-footer {
	background: var(--bg-secondary);
	border-top: 2px solid var(--border-color);
	padding: 24px 0;
	margin-top: 40px;
}

.footer-content {
	display: flex;
	justify-content: space-between;
	align-items: center;
	flex-wrap: wrap;
	gap: 20px;
}

.footer-left {
	color: var(--text-secondary);
	font-size: 14px;
	line-height: 1.6;
}

.footer-left strong {
	color: var(--text-primary);
	font-size: 16px;
}

.footer-right {
	display: flex;
	gap: 12px;
}

.footer-badge {
	background: var(--bg-primary);
	padding: 8px 16px;
	border-radius: var(--radius-md);
	font-size: 13px;
	font-weight: 600;
	color: var(--text-secondary);
	border: 1px solid var(--border-color);
}

.footer-badge i {
	color: #667eea;
	margin-right: 6px;
}

@media (max-width: 768px) {
	.footer-content {
		flex-direction: column;
		text-align: center;
	}
	
	.footer-right {
		justify-content: center;
	}
}
</style>