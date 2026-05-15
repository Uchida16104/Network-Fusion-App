# Network Fusion App

A monorepo scaffold that combines:

- Laravel backend on Render
- Vue.js frontend on Vercel
- TailwindCSS, HTMX, Alpine.js, hyperscript, and sql.js on the frontend
- Network analysis and repair logic on the backend
- Mermaid diagrams and algorithm notes
- Example stubs for C#, Rust, Python (FastAPI/Django/Flask), C++, Carbon, F*, Dafny, Zig, Mojo, and Processing
- Blueprint files managed by `render.yaml` and `vercel.json`

## What this package is

This is a production-oriented starter scaffold, not a fully vendored framework install. It is designed to show how the pieces fit together and to serve as a base for implementation.

## Main idea

1. The Vue frontend sends topology data to the Laravel API.
2. The Laravel backend analyzes the graph, detects issues, and proposes repair actions.
3. The frontend renders the result, plus Mermaid flowcharts and arrow diagrams.
4. Auxiliary language stubs show where specialized analyzers or services can be attached later.

## Folder map

- `backend-laravel/` — Laravel API, Render blueprint, Dockerfile
- `frontend-vue/` — Vue app for Vercel
- `shared/` — Mermaid diagrams, algorithm notes, SQL schema
- `stubs/` — one-file examples for the requested languages

## Deployment

### Render
Use `backend-laravel/render.yaml`.

### Vercel
Use `frontend-vue/vercel.json`.

## API endpoints

- `POST /api/analyze`
- `POST /api/repair`
- `GET /api/health`

## Example payload

```json
{
  "nodes": ["A", "B", "C"],
  "edges": [
    ["A", "B"],
    ["B", "C"]
  ],
  "focus": "A"
}
```

## Notes

- `sql.js` is used client-side in the Vue app for local history storage.
- `php.js` here is represented by a small helper module in `frontend-vue/src/lib/php.js`.
- `Carbon` files use the `.carbon` extension as requested.
- Processing examples include both `.pde` and `.java` files.
