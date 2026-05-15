import initSqlJs from 'sql.js'

let dbPromise = null

export async function openDatabase() {
  if (!dbPromise) {
    dbPromise = initSqlJs({
      locateFile: file => `https://sql.js.org/dist/${file}`,
    }).then(SQL => {
      const db = new SQL.Database()
      db.run('CREATE TABLE IF NOT EXISTS history (id INTEGER PRIMARY KEY, payload TEXT NOT NULL, created_at TEXT NOT NULL)')
      return db
    })
  }
  return dbPromise
}

export async function saveHistory(entry) {
  const db = await openDatabase()
  const stmt = db.prepare('INSERT INTO history (payload, created_at) VALUES (?, datetime("now"))')
  stmt.run([JSON.stringify(entry)])
  stmt.free()
  return db.export()
}
