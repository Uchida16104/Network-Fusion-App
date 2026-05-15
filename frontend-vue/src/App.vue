<template>
  <main class="min-h-screen p-6 md:p-10">
    <section class="mx-auto grid max-w-7xl gap-6 lg:grid-cols-[1.1fr_0.9fr]">
      <div class="panel p-6 md:p-8">
        <div class="flex items-center justify-between gap-4">
          <div>
            <p class="text-xs uppercase tracking-[0.35em] text-cyan-300">Network Fusion</p>
            <h1 class="mt-2 text-3xl font-semibold text-white md:text-4xl">
              Cross-stack network analysis and repair
            </h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
              Vue, TailwindCSS, HTMX, Alpine.js, hyperscript, and sql.js on the frontend, with Laravel on Render as the analysis engine.
            </p>
          </div>
          <button
            class="rounded-full border border-cyan-400/40 bg-cyan-400/10 px-4 py-2 text-sm font-medium text-cyan-200"
            @click="loadSample"
          >
            Load sample
          </button>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
          <label class="block">
            <span class="text-sm text-slate-300">Nodes (comma separated)</span>
            <input v-model="nodesText" class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 outline-none" />
          </label>
          <label class="block">
            <span class="text-sm text-slate-300">Edges (A-B, B-C)</span>
            <input v-model="edgesText" class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 outline-none" />
          </label>
        </div>

        <div class="mt-4 flex flex-wrap gap-3">
          <button class="rounded-xl bg-cyan-500 px-4 py-2 font-medium text-slate-950" @click="analyze">Analyze</button>
          <button class="rounded-xl border border-slate-700 px-4 py-2 font-medium text-slate-100" @click="repair">Repair plan</button>
          <button class="rounded-xl border border-slate-700 px-4 py-2 font-medium text-slate-100" @click="saveLocal">Save local history</button>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
          <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
            <h2 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Mermaid flowchart</h2>
            <pre class="mt-3 overflow-x-auto text-xs text-slate-300">{{ flowchart }}</pre>
          </div>
          <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
            <h2 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Arrow diagram</h2>
            <pre class="mt-3 overflow-x-auto text-xs text-slate-300">{{ arrows }}</pre>
          </div>
        </div>

        <div class="mt-6 rounded-2xl border border-slate-800 bg-slate-950 p-4">
          <h2 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Result</h2>
          <pre class="mt-3 overflow-x-auto text-sm leading-6 text-slate-200">{{ resultText }}</pre>
        </div>

        <div class="mt-6">
          <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4" x-data="{ open: true }">
            <h2 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">HTMX / Alpine / hyperscript zone</h2>
            <div class="mt-3 flex items-center gap-3">
              <button class="rounded-lg border border-slate-700 px-3 py-2 text-sm" x-on:click="open = !open">Toggle</button>
              <span class="text-sm text-slate-300" x-show="open">This block is ready for progressive enhancement.</span>
            </div>
            <div class="mt-4 text-sm text-slate-400" _="on click toggle .hidden on #legacy-note">
              Click handlers can also be expressed with hyperscript.
            </div>
            <p id="legacy-note" class="mt-2 text-sm text-slate-300">Legacy interaction layer placeholder.</p>
          </div>
        </div>
      </div>

      <aside class="panel p-6 md:p-8">
        <h2 class="text-xl font-semibold text-white">Topology snapshot</h2>
        <div class="mt-4 space-y-4">
          <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
            <div class="text-sm text-slate-400">Nodes</div>
            <div class="mt-2 text-lg">{{ parsed.nodes.join(', ') || '—' }}</div>
          </div>
          <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
            <div class="text-sm text-slate-400">Edges</div>
            <div class="mt-2 text-lg">{{ parsed.edges.map(e => e.join(' ↔ ')).join('; ') || '—' }}</div>
          </div>
          <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
            <div class="text-sm text-slate-400">API mode</div>
            <div class="mt-2 text-lg">{{ apiBase }}</div>
          </div>
        </div>

        <div class="mt-6 rounded-2xl border border-slate-800 bg-slate-950 p-4">
          <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Client-side SQL history</h3>
          <p class="mt-2 text-sm text-slate-300">
            The latest analysis is stored in an in-memory sql.js database to demonstrate local analytics support.
          </p>
        </div>

        <div class="mt-6 rounded-2xl border border-slate-800 bg-slate-950 p-4">
          <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">php.js helpers</h3>
          <p class="mt-2 text-sm text-slate-300">
            strlen: {{ phpStrlen(resultText) }}, strpos("repair"): {{ phpStrpos(resultText, 'repair') }}
          </p>
        </div>

        <div class="mt-6 rounded-2xl border border-slate-800 bg-slate-950 p-4">
          <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Deployment targets</h3>
          <p class="mt-2 text-sm text-slate-300">Backend on Render, frontend on Vercel.</p>
        </div>
      </aside>
    </section>
  </main>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import axios from 'axios'
import { strlen as phpStrlen, strpos as phpStrpos } from './lib/php.js'
import { saveHistory } from './lib/sql.js'

const apiBase = import.meta.env.VITE_API_BASE_URL || '/api'

const nodesText = ref('A, B, C, D')
const edgesText = ref('A-B, B-C')
const result = ref({})
const resultText = ref('Ready.')
const parsed = computed(() => {
  const nodes = nodesText.value.split(',').map(s => s.trim()).filter(Boolean)
  const edges = edgesText.value
    .split(',')
    .map(s => s.trim())
    .filter(Boolean)
    .map(pair => pair.split('-').map(x => x.trim()).slice(0, 2))
    .filter(pair => pair.length === 2 && pair[0] && pair[1])
  return { nodes, edges }
})

const flowchart = computed(() => `flowchart TD
  U[User input] --> V[Vue frontend]
  V --> L[Laravel API]
  L --> A[Analyze topology]
  A --> R[Repair plan]
  R --> V
`)

const arrows = computed(() => `User -> Vue -> Laravel -> Analyzer -> Repair -> Vue`)

async function analyze() {
  const payload = { ...parsed.value, focus: parsed.value.nodes[0] || null }
  const { data } = await axios.post(`${apiBase}/analyze`, payload)
  result.value = data
  resultText.value = JSON.stringify(data, null, 2)
  await saveHistory({ kind: 'analyze', payload, data })
}

async function repair() {
  const payload = { ...parsed.value, focus: parsed.value.nodes[0] || null }
  const { data } = await axios.post(`${apiBase}/repair`, payload)
  result.value = data
  resultText.value = JSON.stringify(data, null, 2)
  await saveHistory({ kind: 'repair', payload, data })
}

async function saveLocal() {
  await saveHistory({ kind: 'snapshot', payload: parsed.value, data: result.value })
  resultText.value = `${resultText.value}\n\nSaved to sql.js history.`
}

function loadSample() {
  nodesText.value = 'A, B, C, D, E'
  edgesText.value = 'A-B, B-C, D-E'
}

onMounted(() => {
  resultText.value = 'Loaded. Run Analyze to inspect the topology.'
})
</script>
