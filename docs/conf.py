# docs/conf.py

project = 'Laravel Migration Generator'
author = 'N3XT0R'
release = '8.3.0'
version = release


extensions = [
    'myst_parser',
]

source_suffix = {
    '.md': 'markdown',
}

master_doc = 'index'
exclude_patterns = []

html_theme = 'furo'
html_static_path = ['_static']

# GitHub integration (safe default)
html_context = {
    'display_github': True,
    'github_user': 'N3XT0R',
    'github_repo': 'laravel-migration-generator',
    'github_version': 'master',
    'conf_py_path': '/docs/',
    "sidebar_hide_name": True,
}
