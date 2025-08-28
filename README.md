# Rick & Morty

A web application for exploring characters from the Rick and Morty universe using the [Rick and Morty API](https://rickandmortyapi.com/).

## Features

- 🌌 **Search by Dimension** - Find characters from specific dimensions
- 📍 **Search by Location** - Discover characters by their locations across the multiverse
- 📺 **Search by Episode** - Browse characters that appear in specific episodes
- 📱 **Responsive Design** - Works perfectly on desktop, tablet, and mobile devices

## Technologies Used

- **Backend**: PHP 8+ with Symfony Framework
- **Frontend**: Vanilla JavaScript, HTML5, CSS3
- **Styling**: Tailwind CSS
- **Build Tools**: Webpack Encore
- **API**: Rick and Morty API
- **Containerization**: Docker & Docker Compose

## Getting Started

### Prerequisites

- Docker and Docker Compose
- Node.js and npm/yarn (for asset compilation)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd rickmorty
   ```

2. **Start the application with Docker**
   ```bash
   docker-compose up -d
   ```

3. **Install dependencies and build assets**
   ```bash
   # Install PHP dependencies
   docker-compose exec php composer install
   
   # Install Node.js dependencies
   npm install
   # or
   yarn install
   
   # Build assets
   npm run build
   # or
   yarn build
   ```

4. **Access the application**
   Open your browser and navigate to: `http://localhost:8081`

## API Endpoints

- `GET /` - Homepage with search interface
- `GET /api/characters/dimension?dimension={name}` - Get characters by dimension
- `GET /api/characters/location?location={name}` - Get characters by location
- `GET /api/characters/episode?episode={name}` - Get characters by episode

## Development

### Running in Development Mode

```bash
# Start Docker containers
docker-compose up -d

# Watch for asset changes
npm run watch
# or
yarn watch
```

### Building for Production

```bash
# Build optimized assets
npm run build
# or
yarn build
```

## Docker Configuration

The application runs in Docker containers:

- **PHP Container**: PHP 8+ with Symfony application
- **Nginx Container**: Web server serving the application on port 8081
- **Volume Mounts**: Source code is mounted for development
