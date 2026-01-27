FROM node:18 AS builder

WORKDIR /app

# Copy package manifest(s) then install. Use npm install if lockfile is absent.
COPY package*.json ./
RUN if [ -f package-lock.json ]; then \
			npm ci --silent --no-audit --no-fund; \
		else \
			npm install --silent --no-audit --no-fund; \
		fi

# Copy source and build
COPY . /app
RUN npm run build

## final stage: for dev mode with vite dev server
FROM node:18 AS runner
WORKDIR /app

# Copy node_modules and package.json from builder
COPY --from=builder /app/node_modules /app/node_modules
COPY --from=builder /app/package.json /app/package.json
COPY --from=builder /app/public /app/public

# For dev: need source files (will be mounted via volume in compose)
CMD ["/bin/sh", "-c", "npm run dev -- --host --port ${VITE_PORT:-5173}"]
