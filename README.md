# YouTube to MP3 – Navidrome Website

Aplikacja webowa do pobierania audio z YouTube i integracji z Navidrome.

---

## 🔗 Jak połączyć lokalne repozytorium z tym zdalnym i wysłać pliki

### Scenariusz 1 – masz już lokalne repozytorium Git

```bash
# Przejdź do katalogu projektu
cd /ścieżka/do/twojego/projektu

# Dodaj zdalne repozytorium (origin)
git remote add origin https://github.com/Lamonsky/youtube_to_mp3_navidrome_website.git

# Sprawdź, że remote został dodany poprawnie
git remote -v

# Wyślij lokalną gałąź main do zdalnego repozytorium
git push -u origin main
```

> Jeśli Twoja gałąź nazywa się `master`, użyj:
> ```bash
> git push -u origin master
> ```

---

### Scenariusz 2 – masz pliki, ale NIE masz jeszcze lokalnego repozytorium Git

```bash
# Przejdź do katalogu projektu
cd /ścieżka/do/twojego/projektu

# Zainicjalizuj repozytorium Git
git init

# Dodaj wszystkie pliki do staging area
git add .

# Utwórz pierwszy commit
git commit -m "Initial commit"

# Ustaw domyślną gałąź na main
git branch -M main

# Dodaj zdalne repozytorium
git remote add origin https://github.com/Lamonsky/youtube_to_mp3_navidrome_website.git

# Wyślij pliki do GitHub
git push -u origin main
```

---

### Scenariusz 3 – sklonuj to repozytorium i zacznij od nowa

```bash
# Sklonuj repozytorium
git clone https://github.com/Lamonsky/youtube_to_mp3_navidrome_website.git

# Przejdź do katalogu
cd youtube_to_mp3_navidrome_website

# Skopiuj swoje pliki projektu tutaj, a następnie:
git add .
git commit -m "Add project files"
git push
```

---

## 🔑 Uwierzytelnianie

Jeśli GitHub prosi o hasło, użyj **Personal Access Token (PAT)** zamiast hasła do konta:

1. Przejdź do **GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)**
2. Kliknij **Generate new token**
3. Zaznacz zakres `repo`
4. Skopiuj token i użyj go jako hasła przy `git push`

Możesz też skonfigurować SSH:

```bash
# Wygeneruj klucz SSH
ssh-keygen -t ed25519 -C "twoj@email.com"

# Dodaj klucz publiczny do GitHub: Settings → SSH and GPG keys
cat ~/.ssh/id_ed25519.pub

# Zmień remote URL na SSH
git remote set-url origin git@github.com:Lamonsky/youtube_to_mp3_navidrome_website.git
```

---

## 📁 Struktura projektu

```
youtube_to_mp3_navidrome_website/
├── .gitignore
├── README.md
└── ...
```

---

## 🚀 Przydatne komendy Git

| Komenda | Opis |
| --- | --- |
| `git status` | Sprawdź stan plików |
| `git add .` | Dodaj wszystkie zmiany do staging |
| `git commit -m "wiadomość"` | Zapisz zmiany |
| `git push` | Wyślij zmiany do GitHub |
| `git pull` | Pobierz zmiany ze zdalnego repo |
| `git log --oneline` | Historia commitów |

---

## How to connect a local repository and push files (English)

### If you already have a local Git repo:

```bash
git remote add origin https://github.com/Lamonsky/youtube_to_mp3_navidrome_website.git
git push -u origin main
```

### If you have files but no Git repo yet:

```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/Lamonsky/youtube_to_mp3_navidrome_website.git
git push -u origin main
```
